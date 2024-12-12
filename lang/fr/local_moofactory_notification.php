<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for local_moofactory_notification.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Moofactory Notification';
$string['notifications_category'] = 'Notifications';
$string['settings'] = 'Modifier les paramètres';
$string['managenotif'] = 'Gestion des notifications';
$string['enabled'] = 'Notifications activées';
$string['enabled_desc'] = 'Activation des notifications';
$string['eventstypes'] = 'Types d\'évènements activés';
$string['siteevents'] = 'Evènements de type site';
$string['siteevents_desc'] = 'Notifications des évènements de type site';
$string['daysbeforesiteevent'] = 'Délai (j)';
$string['daysbeforesiteevent_desc'] = 'Délai avant l\'envoi de la notification (en jours)';
$string['hoursbeforesiteevent'] = 'Délai (h)';
$string['hoursbeforesiteevent_desc'] = 'Et/ou délai avant l\'envoi de la notification (en heures)';
$string['siteeventsnotification_desc'] = 'Choix du modèle de notification à utiliser pour les évènements de type site';
$string['coursesevents'] = 'Evènements de type cours';
$string['coursesevents_desc'] = 'Notifications des évènements de type cours';
$string['courseseventsnotification_desc'] = 'Choix du modèle de notification à utiliser pour les évènements de type cours';
$string['usersevents'] = 'Evènements de type utilisateur';
$string['usersevents_desc'] = 'Notifications des évènements de type utilisateur';

$string['coursesaccess'] = 'Non accès aux cours';
$string['coursesaccess_desc'] = 'Notifications en cas de non accès à un cours depuis un certain de temps';
$string['coursesaccesstime'] = 'Intervalle';
$string['coursesaccesstime_desc'] = 'Intervalle de temps à partir duquel une notification est envoyée en cas de non accès à un cours (en jours)';
$string['coursesaccessnotification_desc'] = 'Choix du modèle de notification à utiliser pour les rappels de non accès à un cours';
$string['coursesaccessnotifnumber'] = 'Nombre maximum';
$string['coursesaccessnotifnumber_desc'] = 'Nombre maximum d\'envois de notification entre deux accès au cours';
$string['courseaccess'] = 'Non accès à ce cours';
$string['courseaccesstime'] = 'Depuis';
$string['courseaccesstime_desc'] = 'jours(s)';

$string['coursesenrollments'] = 'Inscriptions aux cours';
$string['coursesenrollments_desc'] = 'Notifications suite à l\'inscription à un cours';
$string['coursesenrollmentstime'] = 'Delai';
$string['coursesenrollmentstime_desc'] = 'Délai avant l\'envoi de la notification (en minutes)';
$string['coursesenrollmentsnotification_desc'] = 'Choix du modèle de notification à utiliser pour les inscriptions à un cours';
$string['courseenrollments'] = 'Inscriptions à ce cours';
$string['courseenrollmentstime'] = 'Delai';
$string['courseenrollmentstime_desc'] = 'minute(s) après l\'inscription à ce cours';

$string['courseevents'] = 'Evènements liés à ce cours';
$string['courseeventscheckavailability'] = 'Tenir compte des restrictions d\'accès aux activités';
$string['courseeventscheckdateavailability'] = 'Ne pas tenir compte des restrictions de type "date"';
$string['courseeventscheckgroupavailability'] = 'Ne pas tenir compte des restrictions de type "groupe"';
$string['usednotification'] = 'Notification utilisée';

$string['daysbeforeevents1'] = 'Premier rappel';
$string['daysbeforeevents1_desc'] = 'jour(s) avant les évènements';
$string['hoursbeforeevents1'] = 'et/ou';
$string['hoursbeforeevents1_desc'] = 'heure(s) avant les évènements';
$string['daysbeforeevents2'] = 'Deuxième rappel';
$string['daysbeforeevents2_desc'] = 'jour(s) avant les évènements';
$string['hoursbeforeevents2'] = 'et/ou';
$string['hoursbeforeevents2_desc'] = 'heure(s) avant les évènements';
$string['daysbeforeevents3'] = 'Troisième rappel';
$string['daysbeforeevents3_desc'] = 'jour(s) avant les évènements';
$string['hoursbeforeevents3'] = 'et/ou';
$string['hoursbeforeevents3_desc'] = 'heure(s) avant les évènements';
$string['menuitem'] = 'Activation des notifications';
$string['module'] = 'Activation des notifications pour ';
$string['moduleevents'] = 'Evènements liés à cette activité';
$string['modulecheckavailability'] = 'Tenir compte des restrictions d\'accès à cette activité';
$string['modulecheckdateavailability'] = 'Ne pas tenir compte des restrictions de type "date"';
$string['modulecheckgroupavailability'] = 'Ne pas tenir compte des restrictions de type "groupe"';
$string['modulereset'] = 'Pour réinitialiser ces valeurs avec celles sauvegardées au niveau du cours, saisir 999 dans les champs concernés ci-dessus.';
$string['notanumber'] = 'La valeur saisie doit être un nombre positif';
$string['notanullnumber'] = 'La valeur saisie doit être un nombre positif non nul';

$string['sendsiteeventsnotifications'] = 'Envoi des notifications pour les évènements de type site';
$string['sendcourseseventsnotifications'] = 'Envoi des notifications pour les évènements de type cours';
$string['sendcourseenrollmentsnotifications'] = 'Envoi des notifications lors des inscriptions aux cours';
$string['sendcourseaccessnotifications'] = 'Envoi des notifications en cas de non accès aux cours depuis un certain de temps';
$string['sendmoduleaccessnotifications'] = 'Envoi des notifications en cas de levé des restrictions d\'une activité';
$string['choose'] = 'Choisir une notification';
$string['notifications'] = 'Notifications';
$string['duplicate'] = 'Dupliquer';
$string['delete'] = 'Supprimer';
$string['add'] = 'Ajouter une notification';
$string['deletenotification'] = 'Supprimer une notification';
$string['deleteplugin'] = 'Cette notification est définie au niveau des paramètres du plugin.';
$string['deletecourses'] = 'Cette notification est définie au niveau des paramètres des cours suivants :';
$string['deletecourse'] = 'Cette notification est définie au niveau des paramètres du cours';
$string['deleteactivities'] = 'Cette notification est définie au niveau des paramètres des activités suivantes :';
$string['deleteactivity'] = 'Cette notification est définie au niveau des paramètres de l\'activité';
$string['deleteconfirm'] = 'Confirmer la suppression de la notification {$a}.<br>Elle sera remplacée par la notification par défaut si besoin.';
$string['required'] = 'Ce champ est requis';
$string['name'] = 'Nom';
$string['type'] = 'Type';
$string['subject'] = 'Objet';
$string['bodyhtml'] = 'Contenu';
$string['nogroup'] = 'aucun groupe';

$string['messageprovider:coursesaccess_notification'] = 'Notifications en cas de non accès aux cours';
$string['messageprovider:coursesenrollments_notification'] = 'Notifications lors des inscriptions aux cours';
$string['messageprovider:coursesevents_notification'] = 'Notifications pour les évènements de type cours';

// Champs de fusion
$string['params'] = 'Paramètres (champs de fusion)';
$string['params_firstname'] = 'Prénom de l\'utilisateur';
$string['params_lastname'] = 'Nom de l\'utilisateur';
$string['params_username'] = 'Identifiant de l\'utilisateur';
$string['params_usergroup'] = 'Groupe de l\'utilisateur dans le cours considéré';
$string['params_eventdate'] = 'Date de l\'évènement';
$string['params_eventname'] = 'Nom de l\'évènement';
$string['params_coursename'] = 'Nom du cours';
$string['params_coursestartdate'] = 'Date de début du cours';
$string['params_courseenddate'] = 'Date de fin du cours ';
$string['params_courseenrolstartdate'] = 'Date du début de l\'inscription de l\'utilisateur dans le cours considéré';
$string['params_courseenrolenddate'] = 'Date du fin de l\'inscription de l\'utilisateur dans le cours considéré';
$string['params_courseurl'] = 'Url du cours';
$string['params_activityname'] = 'Nom de l\'activité';
$string['params_lmsurl'] = 'Url de la plateforme (LMS)';
$string['params_lmsname'] = 'Nom de la plateforme (LMS)';
$string['params_interval'] = 'Intervalle de temps depuis le dernier accès de l\'utilistaeur au cours';
$string['copied'] = 'Copié';

// Capabilities.
$string['moofactory_notification:managenotifications']  = 'Gestion des notifications';
$string['moofactory_notification:setnotifications']  = 'Réglage des notifications';
$string['moofactory_notification:coursesenrollments']  = 'Inscriptions aux cours';
$string['moofactory_notification:coursesevents']  = 'Evènements de type cours';
$string['moofactory_notification:coursesaccess']  = 'Non accès aux cours';
$string['moofactory_notification:modulesaccess']  = 'Levée des restrictions d\'accès';

//champs notification restriction d'accès
$string['moduleaccesstitle'] = 'Notification après levée des restrictions d\'accès';
$string['moduleaccess'] = 'Levée des restrictions d\'accès';
$string['moduleaccess_desc'] = 'Notification suite à la levée des restrictions d\'accès d\'une activité';
$string['leveetime'] = 'Delai';
$string['leveetime_desc'] = 'Délai avant l\'envoi de la notification (en minutes)';
$string['moduleaccessnotification_desc'] = 'Choix du modèle de notification à utiliser pour les levées de restriction';

$string['moduleleveetime_desc'] = 'minute(s)';
$string['daysbeforelevee1'] = 'Premier rappel';
$string['daysbeforelevee1_desc'] = 'jour(s)';
$string['hoursbeforelevee1'] = 'et/ou';
$string['hoursbeforelevee1_desc'] = 'heure(s)';
$string['daysbeforelevee2'] = 'Deuxième rappel';
$string['daysbeforelevee2_desc'] = 'jour(s)';
$string['hoursbeforelevee2'] = 'et/ou';
$string['hoursbeforelevee2_desc'] = 'heure(s)';
$string['daysbeforelevee3'] = 'Troisième rappel';
$string['daysbeforelevee3_desc'] = 'jour(s)';
$string['hoursbeforelevee3'] = 'et/ou';
$string['hoursbeforelevee3_desc'] = 'heure(s)';
