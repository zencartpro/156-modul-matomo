#############################################################################################
# Matomo E-Commerce Tracking 1.0.0 Uninstall - 2021-02-12 - webchills
# NUR AUSF�HREN FALLS SIE DAS MODUL VOLLST�NDIG ENTFERNEN WOLLEN!!!
#############################################################################################

DELETE FROM configuration_group WHERE configuration_group_title = 'Matomo';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_MODUL_VERSION';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_URL';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_ID';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_REPORT_PERIOD';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_REPORT_DATE';
DELETE FROM configuration WHERE configuration_key = 'MATOMO_TOKEN_AUTH';
DELETE FROM configuration_language WHERE configuration_key = 'MATOMO_URL';
DELETE FROM configuration_language WHERE configuration_key = 'MATOMO_ID';
DELETE FROM configuration_language WHERE configuration_key = 'MATOMO_REPORT_PERIOD';
DELETE FROM configuration_language WHERE configuration_key = 'MATOMO_REPORT_DATE';
DELETE FROM configuration_language WHERE configuration_key = 'MATOMO_TOKEN_AUTH';
DELETE FROM admin_pages WHERE page_key='configMatomo';
DELETE FROM admin_pages WHERE page_key='statsMatomo';