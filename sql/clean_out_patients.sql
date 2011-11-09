delete from ar_activity;
delete from ar_session;
delete from billing;
delete from categories_to_documents;
delete from chart_tracker;
delete from claims;
delete from documents;
delete from drug_inventory;
delete from drug_sales;
delete from employer_data;
delete from forms;
delete from form_encounter;
delete from form_ros;
delete from form_vitals;
delete from history_data;
delete from insurance_data;
delete from issue_encounter;
delete from lbf_data;
delete from lists;
delete from lists_ippf_con;
delete from lists_ippf_gcac;
delete from lists_ippf_srh;
delete from log;
delete from openemr_postcalendar_events where pc_pid != '';
delete from patient_data;
delete from payments;
delete from pnotes;
delete from prescriptions;
delete from transactions;
alter table ar_activity    auto_increment = 1;
alter table ar_session     auto_increment = 1;
alter table billing        auto_increment = 1;
alter table claims         auto_increment = 1;
alter table drug_inventory auto_increment = 1;
alter table drug_sales     auto_increment = 1;
alter table employer_data  auto_increment = 1;
alter table forms          auto_increment = 1;
alter table form_encounter auto_increment = 1;
alter table form_ros       auto_increment = 1;
alter table form_vitals    auto_increment = 1;
alter table history_data   auto_increment = 1;
alter table insurance_data auto_increment = 1;
alter table lbf_data       auto_increment = 1;
alter table lists          auto_increment = 1;
alter table log            auto_increment = 1;
alter table patient_data   auto_increment = 1;
alter table payments       auto_increment = 1;
alter table pnotes         auto_increment = 1;
alter table prescriptions  auto_increment = 1;
alter table transactions   auto_increment = 1;
update sequences set id = 1;

