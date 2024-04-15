<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2017 Mikael Carlavan <contact@mika-carl.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/sticky/class/actions_sticky.class.php
 *  \ingroup    sticky
 *  \brief      File of class to manage actions on propal
 */

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/ticket/class/ticket.class.php';

class ActionsSticky
{
    function doActions($parameters, &$object, &$action, $hookmanager)
    {
        global $user, $langs;

        $res = 0;

        if ($object->element == 'ticket') {
            $permissiontoadd = $user->hasRight('ticket', 'write');

            if ($action == 'confirm_set_status' && $permissiontoadd && !GETPOST('cancel')) {
                if ($object->fetch(GETPOST('id', 'int'), GETPOST('track_id', 'alpha')) >= 0) {

                    $new_status = GETPOST('new_status', 'int');

                    if ($new_status == Ticket::STATUS_NOT_READ) {
                        $object->progress = 0;
                    } else if ($new_status == Ticket::STATUS_READ) {
                        $object->progress = 10;
                    } else if ($new_status == Ticket::STATUS_ASSIGNED) {
                        $object->progress = 20;
                    } else if ($new_status == Ticket::STATUS_IN_PROGRESS) {
                        $object->progress = 60;
                    } else if ($new_status == Ticket::STATUS_NEED_MORE_INFO) {
                        $object->progress = 40;
                    } else if ($new_status == Ticket::STATUS_WAITING) {
                        $object->progress = 35;
                    } else if ($new_status == Ticket::STATUS_CLOSED) {
                        $object->progress = 100;
                    } else if ($new_status == Ticket::STATUS_CANCELED) {
                        $object->progress = 100;
                    }

                    $object->update($user, 1);
                }
            }

            if ($action == 'set_read' && $permissiontoadd && !GETPOST('cancel')) {
                $object->progress = 10;
                $object->update($user, 1);
            }
        }

        if ($object->element == 'societe') {
            if (($action == 'add' || $action == 'update') && $user->hasRight('societe', 'creer')) {
                if (!GETPOST('tva_intra')) {
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("VATIntra")), null, 'errors');
                    $res = 1;
                }

                if (!GETPOST('address')) {
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ThirdPartyAddress")), null, 'errors');
                    $res = 1;
                }

                if (!GETPOST('email')) {
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ThirdPartyEmail")), null, 'errors');
                    $res = 1;
                }

                if (!GETPOST('phone')) {
                    setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Phone")), null, 'errors');
                    $res = 1;
                }

                if ($res) {
                    $action = ($action == 'add' ? 'create' : 'edit');
                }
            }
        }

        return $res;
    }

    function printMainArea($parameters, &$object, &$action, $hookmanager)
    {
        global $user;

        if (in_array('index', $hookmanager->contextarray) && !$user->admin) {
            $out2 = '<script type="text/javascript">' . "\r\n";
            $out2 .= '$(document).ready(function(){' . "\r\n";
            $out2 .= '   window.location = "'.dol_buildpath('/sticky/index.php', 1).'";' . "\r\n";
            $out2 .= '});' . "\r\n";
            $out2 .= '</script>' . "\r\n";

            print $out2;
        }
    }

    function setHtmlTitle($parameters, &$object, &$action, $hookmanager)
    {
        $this->resprints = 'Sticky';
        return 1;
    }

    function beforeBodyClose()
    {
        $out2 = '<script type="text/javascript">' . "\r\n";
        $out2 .= '$(document).ready(function(){' . "\r\n";
        $out2 .= '   $("div.user-body").remove();' . "\r\n";
        $out2 .= '});' . "\r\n";
        $out2 .= '</script>' . "\r\n";

        print $out2;

        return 0;
    }

    function printTopRightMenu($parameters, &$object, &$action, $hookmanager)
    {
        global $mysoc, $conf, $user;

        $urllogo = '';
        if (!empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small)) {
            $urllogo = DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_small);
        } elseif (!empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo)) {
            $urllogo = DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/'.$mysoc->logo);
        }

        $this->resprints = '<div class="inline-block" style="margin-right: 5px"><img height="40px" alt="" src="'.$urllogo.'" id="" /></div>';
        return 0;
    }
}


