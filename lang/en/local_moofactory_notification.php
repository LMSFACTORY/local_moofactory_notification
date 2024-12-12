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
 * English strings for local_moofactory_notification.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Moofactory Notification';
$string['notifications_category'] = 'Notifications';
$string['settings'] = 'Settings';
$string['managenotif'] = 'Manage notifications';
$string['enabled'] = 'Notifications activated';
$string['enabled_desc'] = 'Notifications activation';
$string['eventstypes'] = 'Types of events enabled';
$string['siteevents'] = 'Site events';
$string['siteevents_desc'] = 'Site events notifications';
$string['daysbeforesiteevent'] = 'Time (d)';
$string['daysbeforesiteevent_desc'] = 'Time before notification is sent (in days)';
$string['hoursbeforesiteevent'] = 'Time (h)';
$string['hoursbeforesiteevent_desc'] = 'And/or time before notification is sent (in hours)';
$string['siteeventsnotification_desc'] = 'Choice of the notification template to use for site events';
$string['coursesevents'] = 'Courses events';
$string['coursesevents_desc'] = 'Courses events notifications';
$string['courseseventsnotification_desc'] = 'Choice of the notification template to use for courses events';
$string['usersevents'] = 'Users events';
$string['usersevents_desc'] = 'Users events notifications';

$string['coursesaccess'] = 'Non-access to courses';
$string['coursesaccess_desc'] = 'Notifications in the event of non-access to a course for a certain time';
$string['coursesaccesstime'] = 'Time interval';
$string['coursesaccesstime_desc'] = 'Time interval from which a notification is sent in the event of non-access to a course (in days)';
$string['coursesaccessnotification_desc'] = 'Choice of the notification template to use for non-access reminders to a course';
$string['coursesaccessnotifnumber'] = 'Maximum number';
$string['coursesaccessnotifnumber_desc'] = 'Maximum number of notifications sent between two course accesses';
$string['courseaccess'] = 'Non-access to this course';
$string['courseaccesstime'] = 'For';
$string['courseaccesstime_desc'] = 'day(s)';

$string['coursesenrollments'] = 'Courses enrollments';
$string['coursesenrollments_desc'] = 'Notifications when a course enrollment occurs';
$string['coursesenrollmentstime'] = 'Time';
$string['coursesenrollmentstime_desc'] = 'Time before notification is sent (in minutes)';
$string['coursesenrollmentsnotification_desc'] = 'Choice of the notification template to use for courses enrollments';
$string['courseenrollments'] = 'Enrollments to this course';
$string['courseenrollmentstime'] = 'Time';
$string['courseenrollmentstime_desc'] = 'minute(s) after enrollment to this course';

$string['courseevents'] = 'Events link to this course';
$string['courseeventscheckavailability'] = 'Take into account access restrictions to activities';
$string['courseeventscheckdateavailability'] = 'Ignore "date" type restrictions';
$string['courseeventscheckgroupavailability'] = 'Ignore "group" type restrictions';
$string['usednotification'] = 'Notification used';

$string['daysbeforeevents1'] = 'First reminder';
$string['daysbeforeevents1_desc'] = 'day(s) before the events';
$string['hoursbeforeevents1'] = 'and/or';
$string['hoursbeforeevents1_desc'] = 'hour(s) before the events';
$string['daysbeforeevents2'] = 'Second reminder';
$string['daysbeforeevents2_desc'] = 'day(s) before the events';
$string['hoursbeforeevents2'] = 'and/or';
$string['hoursbeforeevents2_desc'] = 'hour(s) before the events';
$string['daysbeforeevents3'] = 'Third reminder';
$string['daysbeforeevents3_desc'] = 'day(s) before the events';
$string['hoursbeforeevents3'] = 'and/or';
$string['hoursbeforeevents3_desc'] = 'hour(s) before the events';
$string['menuitem'] = 'Activate notifications';
$string['module'] = 'Activate notifications for ';
$string['moduleevents'] = 'Events link to this activity';
$string['modulecheckavailability'] = 'Take into account access restrictions to this activity';
$string['modulecheckdateavailability'] = 'Ignore "date" type restrictions';
$string['modulecheckgroupavailability'] = 'Ignore "group" type restrictions';
$string['modulereset'] = 'To reset these values ​​with those saved at the course level, enter 999 in the fields concerned above.';
$string['notanumber'] = 'The value entered must be a positive number';
$string['notanullnumber'] = 'The value entered must be a not null positive number';

$string['sendsiteeventsnotifications'] = 'Sending notifications for site events';
$string['sendcourseseventsnotifications'] = 'Sending notifications for courses events';
$string['sendcourseenrollmentsnotifications'] = 'Sending notifications for courses enrollments';
$string['sendcourseaccessnotifications'] = 'Sending notifications in the event of non-access to courses for a certain time';
$string['sendmoduleaccessnotifications'] = 'Sending notifications after lifting of access restrictions';
$string['choose'] = 'Choose a notification';
$string['notifications'] = 'Notifications';
$string['duplicate'] = 'Duplicate';
$string['delete'] = 'Delete';
$string['add'] = 'Add a notification';
$string['deletenotification'] = 'Delete a notification';
$string['deleteplugin'] = 'This notification is defined in the plugin settings.';
$string['deletecourses'] = 'This notification is set in the following course settings:';
$string['deletecourse'] = 'This notification is defined in the settings of the course';
$string['deleteactivities'] = 'This notification is set in the settings of the following activities:';
$string['deleteactivity'] = 'This notification is defined in the settings of the activity';
$string['deleteconfirm'] = 'Confirm the deletion of the notification {$a}.<br>It will be replaced by the default notification if needed.';
$string['required'] = 'This field is required';
$string['name'] = 'Name';
$string['type'] = 'Type';
$string['subject'] = 'Subject';
$string['bodyhtml'] = 'Content';
$string['nogroup'] = 'no group';

// Champs de fusion
$string['params'] = 'Parameters (merge fields)';
$string['params_firstname'] = 'User\'s first name';
$string['params_lastname'] = 'User\'s last name';
$string['params_username'] = 'User ID';
$string['params_usergroup'] = 'Group of the user in the considered course';
$string['params_eventdate'] = 'Event date';
$string['params_eventname'] = 'Event name';
$string['params_coursename'] = 'Course name';
$string['params_coursestartdate'] = 'Course start date';
$string['params_courseenddate'] = 'Course end date ';
$string['params_courseenrolstartdate'] = 'Date of the start of the user\'s enrolment in the considered course';
$string['params_courseenrolenddate'] = 'Date of the end of the user\'s enrolment in the considered course';
$string['params_courseurl'] = 'Course URL';
$string['params_activityname'] = 'Activity name';
$string['params_lmsurl'] = 'Platform URL (LMS)';
$string['params_lmsname'] = 'Platform name (LMS)';
$string['params_interval'] = 'Time interval since last user access to course';

$string['messageprovider:coursesaccess_notification'] = 'Notifications in the event of non-access to courses';
$string['messageprovider:coursesenrollments_notification'] = 'Notifications for courses enrollments';
$string['messageprovider:coursesevents_notification'] = 'Notifications for courses events';
$string['copied'] = 'Copied';

// Capabilities.
$string['moofactory_notification:managenotifications']  = 'Manage notifications';
$string['moofactory_notification:setnotifications']  = 'Setting notifications';
$string['moofactory_notification:coursesenrollments']  = 'Courses enrollments';
$string['moofactory_notification:coursesevents']  = 'Courses events';
$string['moofactory_notification:coursesaccess']  = 'Non-access to courses';
$string['moofactory_notification:modulesaccess']  = 'Lifting of access restrictions';

//champs notification restriction d'accès
$string['moduleaccesstitle'] = 'Notification after lifting of access restrictions';
$string['moduleaccess'] = 'Lifting of access restrictions';
$string['moduleaccess_desc'] = 'Notification after the lifting of access restrictions';
$string['leveetime'] = 'Delay';
$string['leveetime_desc'] = 'Delay before lifting of access restrictions (in minutes)';
$string['moduleaccessnotification_desc'] = 'Choice of the notification to use for the lifting of access restrictions';

$string['moduleleveetime_desc'] = 'minute(s)';
$string['daysbeforelevee1'] = 'First reminder';
$string['daysbeforelevee1_desc'] = 'day(s)';
$string['hoursbeforelevee1'] = 'and/or';
$string['hoursbeforelevee1_desc'] = 'hour(s)';
$string['daysbeforelevee2'] = 'Second reminder';
$string['daysbeforelevee2_desc'] = 'day(s)';
$string['hoursbeforelevee2'] = 'and/or';
$string['hoursbeforelevee2_desc'] = 'hour(s)';
$string['daysbeforelevee3'] = 'Third reminder';
$string['daysbeforelevee3_desc'] = 'day(s)';
$string['hoursbeforelevee3'] = 'and/or';
$string['hoursbeforelevee3_desc'] = 'hour(s)';