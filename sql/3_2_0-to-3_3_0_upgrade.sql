#IfNotRow categories name Advance Directive
  INSERT INTO categories select (select MAX(id) from categories) + 1, 'Advance Directive', '', 1, rght, rght + 7 from categories where name = 'Categories';
  INSERT INTO categories select (select MAX(id) from categories) + 1, 'Do Not Resuscitate Order', '', (select id from categories where name = 'Advance Directive'), rght + 1, rght + 2 from categories where name = 'Categories';
  INSERT INTO categories select (select MAX(id) from categories) + 1, 'Durable Power of Attorney', '', (select id from categories where name = 'Advance Directive'), rght + 3, rght + 4 from categories where name = 'Categories';
  INSERT INTO categories select (select MAX(id) from categories) + 1, 'Living Will', '', (select id from categories where name = 'Advance Directive'), rght + 5, rght + 6 from categories where name = 'Categories';
  UPDATE categories SET rght = rght + 8 WHERE name = 'Categories';
  UPDATE categories_seq SET id = (select MAX(id) from categories);
#EndIf

#IfMissingColumn patient_data completed_ad
ALTER TABLE patient_data
  ADD completed_ad VARCHAR(3) NOT NULL DEFAULT 'NO',
  ADD ad_reviewed date DEFAULT NULL;
#EndIf

#IfNotRow2D list_options list_id lists option_id apptstat
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists'   ,'apptstat','Appointment Statuses', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','-'       ,'- None'              , 5,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','*'       ,'* Reminder done'     ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','+'       ,'+ Chart pulled'      ,15,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','x'       ,'x Canceled'          ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','?'       ,'? No show'           ,25,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','@'       ,'@ Arrived'           ,30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','~'       ,'~ Arrived late'      ,35,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','!'       ,'! Left w/o visit'    ,40,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','#'       ,'# Ins/fin issue'     ,45,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','<'       ,'< In exam room'      ,50,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','>'       ,'> Checked out'       ,55,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','$'       ,'$ Coding done'       ,60,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('apptstat','%'       ,'% Canceled < 24h'    ,65,0);
ALTER TABLE openemr_postcalendar_events CHANGE pc_apptstatus pc_apptstatus varchar(15) NOT NULL DEFAULT '-';
#EndIf

#IfNotRow2D layout_options form_id DEM field_id referral_source
INSERT INTO `layout_options` VALUES ('DEM', 'referral_source', '5Stats', 'Referral Source',10, 26, 1, 0, 0, 'refsource', 1, 1, '', '', 'How did they hear about us');
#EndIf

#IfMissingColumn list_options notes
ALTER TABLE list_options
  CHANGE mapping mapping varchar(31) NOT NULL DEFAULT '',
  ADD notes varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D list_options list_id lists option_id warehouse
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','warehouse','Warehouses',21,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('warehouse','onsite','On Site', 5,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id abook_type
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','abook_type'  ,'Address Book Types'  , 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','ord_img','Imaging Service'     , 5,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','ord_imm','Immunization Service',10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','ord_lab','Lab Service'         ,15,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','spe'    ,'Specialist'          ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','vendor' ,'Vendor'              ,25,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('abook_type','oth'    ,'Other'               ,95,0);
#EndIf

#IfMissingColumn users abook_type
ALTER TABLE users
  ADD abook_type varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_type
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_type','Procedure Types', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_type','grp','Group'          ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_type','ord','Procedure Order',20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_type','res','Discrete Result',30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_type','rec','Recommendation' ,40,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_body_site
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_body_site','Procedure Body Sites', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_body_site','arm'    ,'Arm'    ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_body_site','buttock','Buttock',20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_body_site','oth'    ,'Other'  ,90,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_specimen
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_specimen','Procedure Specimen Types', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_specimen','blood' ,'Blood' ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_specimen','saliva','Saliva',20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_specimen','urine' ,'Urine' ,30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_specimen','oth'   ,'Other' ,90,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_route
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_route','Procedure Routes', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_route','inj' ,'Injection',10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_route','oral','Oral'     ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_route','oth' ,'Other'    ,90,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_lat
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_lat','Procedure Lateralities', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_lat','left' ,'Left'     ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_lat','right','Right'    ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_lat','bilat','Bilateral',30,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_unit
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','proc_unit','Procedure Units', 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','bool'       ,'Boolean'    ,  5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','cu_mm'      ,'CU.MM'      , 10);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','fl'         ,'FL'         , 20);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','g_dl'       ,'G/DL'       , 30);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','gm_dl'      ,'GM/DL'      , 40);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','hmol_l'     ,'HMOL/L'     , 50);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','iu_l'       ,'IU/L'       , 60);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','mg_dl'      ,'MG/DL'      , 70);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','mil_cu_mm'  ,'Mil/CU.MM'  , 80);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','percent'    ,'Percent'    , 90);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','percentile' ,'Percentile' ,100);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','pg'         ,'PG'         ,110);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','ratio'      ,'Ratio'      ,120);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','thous_cu_mm','Thous/CU.MM',130);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','units'      ,'Units'      ,140);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','units_l'    ,'Units/L'    ,150);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','days'       ,'Days'       ,600);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','weeks'      ,'Weeks'      ,610);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','months'     ,'Months'     ,620);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('proc_unit','oth'        ,'Other'      ,990);
#EndIf

#IfNotRow2D list_options list_id lists option_id ord_priority
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','ord_priority','Order Priorities', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_priority','high'  ,'High'  ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_priority','normal','Normal',20,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id ord_status
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','ord_status','Order Statuses', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_status','pending' ,'Pending' ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_status','routed'  ,'Routed'  ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_status','complete','Complete',30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ord_status','canceled','Canceled',40,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_rep_status
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_rep_status','Procedure Report Statuses', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_rep_status','final' ,'Final'      ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_rep_status','review','Reviewed'   ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_rep_status','prelim','Preliminary',30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_rep_status','cancel','Canceled'   ,40,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_rep_status','error' ,'Error'      ,50,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_res_abnormal
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_res_abnormal','Procedure Result Abnormal', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_abnormal','no'  ,'No'  ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_abnormal','yes' ,'Yes' ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_abnormal','high','High',30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_abnormal','low' ,'Low' ,40,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_res_status
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_res_status','Procedure Result Statuses', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_status','final' ,'Final'      ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_status','prelim','Preliminary',20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_status','cancel','Canceled'   ,30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_status','error' ,'Error'      ,40,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id proc_res_bool
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','proc_res_bool','Procedure Boolean Results', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_bool','neg' ,'Negative',10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('proc_res_bool','pos' ,'Positive',20,0);
#EndIf

#IfNotTable procedure_type
CREATE TABLE `procedure_type` (
  `procedure_type_id`   bigint(20)   NOT NULL AUTO_INCREMENT,
  `parent`              bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references procedure_type.procedure_type_id',
  `name`                varchar(63)  NOT NULL DEFAULT '' COMMENT 'name for this category, procedure or result type',
  `lab_id`              bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references users.id, 0 means default to parent',
  `procedure_code`      varchar(31)  NOT NULL DEFAULT '' COMMENT 'code identifying this procedure',
  `procedure_type`      varchar(31)  NOT NULL DEFAULT '' COMMENT 'lab, imaging, ...',
  `body_site`           varchar(31)  NOT NULL DEFAULT '' COMMENT 'where to do injection, e.g. arm, buttok',
  `specimen`            varchar(31)  NOT NULL DEFAULT '' COMMENT 'blood, urine, saliva, etc.',
  `route_admin`         varchar(31)  NOT NULL DEFAULT '' COMMENT 'oral, injection',
  `laterality`          varchar(31)  NOT NULL DEFAULT '' COMMENT 'left, right, ...',
  `description`         varchar(255) NOT NULL DEFAULT '' COMMENT 'descriptive text for procedure_code',
  `standard_code`       varchar(255) NOT NULL DEFAULT '' COMMENT 'industry standard code type and code (e.g. CPT4:12345)',
  `related_code`        varchar(255) NOT NULL DEFAULT '' COMMENT 'suggested code(s) for followup services if result is abnormal',
  `units`               varchar(31)  NOT NULL DEFAULT '' COMMENT 'default for procedure_result.units',
  `range`               varchar(255) NOT NULL DEFAULT '' COMMENT 'default for procedure_result.range',
  `seq`                 int(11)      NOT NULL default 0  COMMENT 'sequence number for ordering',
  PRIMARY KEY (`procedure_type_id`),
  KEY parent (parent)
) ENGINE=MyISAM;
#EndIf

#IfNotTable procedure_order
CREATE TABLE `procedure_order` (
  `procedure_order_id`     bigint(20)   NOT NULL AUTO_INCREMENT,
  `procedure_type_id`      bigint(20)   NOT NULL            COMMENT 'references procedure_type.procedure_type_id',
  `provider_id`            bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references users.id',
  `patient_id`             bigint(20)   NOT NULL            COMMENT 'references patient_data.pid',
  `encounter_id`           bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references form_encounter.encounter',
  `date_collected`         datetime     DEFAULT NULL        COMMENT 'time specimen collected',
  `date_ordered`           date         DEFAULT NULL,
  `order_priority`         varchar(31)  NOT NULL DEFAULT '',
  `order_status`           varchar(31)  NOT NULL DEFAULT '' COMMENT 'pending,routed,complete,canceled',
  `patient_instructions`   text         NOT NULL DEFAULT '',
  `activity`               tinyint(1)   NOT NULL DEFAULT 1  COMMENT '0 if deleted',
  PRIMARY KEY (`procedure_order_id`),
  KEY datepid (date_ordered, patient_id)
) ENGINE=MyISAM;
#EndIf

#IfNotTable procedure_report
CREATE TABLE `procedure_report` (
  `procedure_report_id` bigint(20)     NOT NULL AUTO_INCREMENT,
  `procedure_order_id`  bigint(20)     DEFAULT NULL   COMMENT 'references procedure_order.procedure_order_id',
  `date_collected`      datetime       DEFAULT NULL,
  `date_report`         date           DEFAULT NULL,
  `source`              bigint(20)     NOT NULL DEFAULT 0  COMMENT 'references users.id, who entered this data',
  `specimen_num`        varchar(63)    NOT NULL DEFAULT '',
  `report_status`       varchar(31)    NOT NULL DEFAULT '' COMMENT 'received,complete,error',
  PRIMARY KEY (`procedure_report_id`),
  KEY procedure_order_id (procedure_order_id)
) ENGINE=MyISAM; 
#EndIf

#IfNotTable procedure_result
CREATE TABLE `procedure_result` (
  `procedure_result_id` bigint(20)   NOT NULL AUTO_INCREMENT,
  `procedure_report_id` bigint(20)   NOT NULL            COMMENT 'references procedure_report.procedure_report_id',
  `procedure_type_id`   bigint(20)   NOT NULL            COMMENT 'references procedure_type.procedure_type_id',
  `date`                datetime     DEFAULT NULL        COMMENT 'lab-provided date specific to this result',
  `facility`            varchar(255) NOT NULL DEFAULT '' COMMENT 'lab-provided testing facility ID',
  `units`               varchar(31)  NOT NULL DEFAULT '',
  `result`              varchar(255) NOT NULL DEFAULT '',
  `range`               varchar(255) NOT NULL DEFAULT '',
  `abnormal`            varchar(31)  NOT NULL DEFAULT '' COMMENT 'no,yes,high,low',
  `comments`            text         NOT NULL DEFAULT '' COMMENT 'comments from the lab',
  `document_id`         bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references documents.id if this result is a document',
  `result_status`       varchar(31)  NOT NULL DEFAULT '' COMMENT 'preliminary, cannot be done, final, corrected, incompete...etc.',
  PRIMARY KEY (`procedure_result_id`),
  KEY procedure_report_id (procedure_report_id)
) ENGINE=MyISAM; 
#EndIf

#IfMissingColumn drug_inventory warehouse_id
ALTER TABLE drug_inventory
  ADD warehouse_id varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn drug_inventory vendor_id
ALTER TABLE drug_inventory
  ADD vendor_id bigint(20) NOT NULL DEFAULT 0;
#EndIf

#IfMissingColumn drug_sales xfer_inventory_id
ALTER TABLE drug_sales
  ADD xfer_inventory_id int(11) NOT NULL DEFAULT 0;
#EndIf

#IfMissingColumn drugs allow_combining
ALTER TABLE drugs
  ADD allow_combining tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = allow filling an order from multiple lots',
  ADD allow_multiple  tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = allow multiple lots at one warehouse';
#EndIf

#IfNotRow registry directory procedure_order
INSERT INTO `registry` VALUES ('Procedure Order', 1, 'procedure_order', NULL, 1, 1, '2010-02-25 00:00:00', 0, 'Administrative', '');
#EndIf

UPDATE registry SET category = 'Administrative' WHERE category = 'category' AND directory = 'fee_sheet';
UPDATE registry SET category = 'Administrative' WHERE category = 'category' AND directory = 'procedure_order';
UPDATE registry SET category = 'Administrative' WHERE category = 'category' AND directory = 'newpatient';
UPDATE registry SET category = 'Administrative' WHERE category = 'category' AND directory = 'misc_billing_options';
UPDATE registry SET category = 'Clinical' WHERE category = 'category';

#IfMissingColumn users default_warehouse
ALTER TABLE users ADD default_warehouse varchar(31) NOT NULL DEFAULT '';
#EndIf

UPDATE layout_options SET edit_options = 'N'  WHERE form_id = 'DEM' AND field_id = 'title'  AND edit_options = '';
UPDATE layout_options SET edit_options = 'CD' WHERE form_id = 'DEM' AND field_id = 'fname'  AND edit_options = 'C';
UPDATE layout_options SET edit_options = 'CD' WHERE form_id = 'DEM' AND field_id = 'lname'  AND edit_options = 'C';
UPDATE layout_options SET edit_options = 'ND' WHERE form_id = 'DEM' AND field_id = 'pubpid' AND edit_options = '';
UPDATE layout_options SET edit_options = 'N'  WHERE form_id = 'DEM' AND field_id = 'sex'    AND edit_options = '';

#IfNotTable globals
CREATE TABLE `globals` (
  `gl_name`             varchar(63)    NOT NULL,
  `gl_index`            int(11)        NOT NULL DEFAULT 0,
  `gl_value`            varchar(255)   NOT NULL DEFAULT '',
  PRIMARY KEY (`gl_name`, `gl_index`)
) ENGINE=MyISAM; 
#EndIf

#IfMissingColumn lists reinjury_id
ALTER TABLE lists
  ADD reinjury_id bigint(20)  NOT NULL DEFAULT 0,
  ADD injury_part varchar(31) NOT NULL DEFAULT '',
  ADD injury_type varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D list_options list_id lists option_id irnpool
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists'   ,'irnpool','Invoice Reference Number Pools', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, notes ) VALUES ('irnpool','main','Main',1,1,'000001');
#EndIf

#IfMissingColumn users irnpool
ALTER TABLE users ADD irnpool varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn form_encounter invoice_refno
ALTER TABLE form_encounter ADD invoice_refno varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn drug_sales notes
ALTER TABLE drug_sales
  ADD notes varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotTable code_types
CREATE TABLE code_types (
  ct_key  varchar(15) NOT NULL           COMMENT 'short alphanumeric name',
  ct_id   int(11)     UNIQUE NOT NULL    COMMENT 'numeric identifier',
  ct_seq  int(11)     NOT NULL DEFAULT 0 COMMENT 'sort order',
  ct_mod  int(11)     NOT NULL DEFAULT 0 COMMENT 'length of modifier field',
  ct_just varchar(15) NOT NULL DEFAULT ''COMMENT 'ct_key of justify type, if any',
  ct_mask varchar(9)  NOT NULL DEFAULT ''COMMENT 'formatting mask for code values',
  ct_fee  tinyint(1)  NOT NULL default 0 COMMENT '1 if fees are used',
  ct_rel  tinyint(1)  NOT NULL default 0 COMMENT '1 if can relate to other code types',
  ct_nofs tinyint(1)  NOT NULL default 0 COMMENT '1 if to be hidden in the fee sheet',
  ct_diag tinyint(1)  NOT NULL default 0 COMMENT '1 if this is a diagnosis type',
  PRIMARY KEY (ct_key)
) ENGINE=MyISAM;
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('ICD9' , 2, 1, 2, ''    , 0, 0, 0, 1);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('CPT4' , 1, 2, 2, 'ICD9', 1, 0, 0, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('HCPCS', 3, 3, 2, 'ICD9', 1, 0, 0, 0);
#EndIf

#IfNotRow2D list_options list_id lists option_id code_types
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists', 'code_types', 'Code Types', 1);
#EndIf

ALTER TABLE layout_options CHANGE description description text;

#IfMissingColumn transactions refer_reply_date
ALTER TABLE transactions ADD refer_reply_date date DEFAULT NULL;
#EndIf

#IfMissingColumn transactions reply_related_code
ALTER TABLE transactions ADD reply_related_code varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotTable version
CREATE TABLE version (
  v_major    int(11)     NOT NULL DEFAULT 0,
  v_minor    int(11)     NOT NULL DEFAULT 0,
  v_patch    int(11)     NOT NULL DEFAULT 0,
  v_tag      varchar(31) NOT NULL DEFAULT '',
  v_database int(11)     NOT NULL DEFAULT 0
) ENGINE=MyISAM;
INSERT INTO version (v_major, v_minor, v_patch, v_tag, v_database) VALUES (0, 0, 0, '', 0);
#EndIf

#IfMissingColumn lists injury_grade
ALTER TABLE lists
  ADD injury_grade varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn form_encounter referral_source
ALTER TABLE form_encounter ADD referral_source varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn drugs max_level
ALTER TABLE drugs ADD max_level float NOT NULL DEFAULT 0.0;
ALTER TABLE drugs CHANGE reorder_point reorder_point float NOT NULL DEFAULT 0.0;
#EndIf

#IfMissingColumn drug_sales distributor_id
ALTER TABLE drug_sales ADD distributor_id bigint(20) NOT NULL DEFAULT 0;
#EndIf

#IfNotRow2D list_options list_id abook_type option_id dist
INSERT INTO list_options (list_id,option_id,title,seq,is_default) VALUES ('abook_type','dist','Distributor',30,0);
#EndIf

#IfNotTable product_warehouse
CREATE TABLE `product_warehouse` (
  `pw_drug_id`   int(11) NOT NULL,
  `pw_warehouse` varchar(31) NOT NULL,
  `pw_min_level` float       DEFAULT 0,
  `pw_max_level` float       DEFAULT 0,
  PRIMARY KEY  (`pw_drug_id`,`pw_warehouse`)
) ENGINE=MyISAM;
#EndIf

#IfNotRow2D list_options list_id lists option_id reftype
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists', 'reftype', 'Referral Types', 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('reftype','2' ,'Outbound External',10);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('reftype','3' ,'Outbound Internal',20);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('reftype','4' ,'Inbound External' ,30);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('reftype','5' ,'Inbound Internal' ,40);
UPDATE layout_options SET data_type = 14 WHERE form_id = 'REF' AND field_id = 'refer_from';
UPDATE layout_options SET title='Referral Type', list_id = 'reftype'  WHERE form_id = 'REF' AND field_id = 'refer_external' AND list_id = 'boolean';
UPDATE transactions SET refer_external = '2' WHERE title = 'Referral' AND refer_external = '1';
UPDATE transactions SET refer_external = '3' WHERE title = 'Referral' AND refer_external = '0';
#EndIf

#IfMissingColumn facility latitude
ALTER TABLE facility ADD latitude varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn facility longitude
ALTER TABLE facility ADD longitude varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotIndex forms pid_encounter
CREATE INDEX `pid_encounter` ON `forms` (`pid`, `encounter`);
#EndIf

#IfNotIndex drug_sales pid_encounter
CREATE INDEX `pid_encounter` ON `drug_sales` (`pid`, `encounter`);
#EndIf

#IfNotIndex form_encounter date
CREATE INDEX `date` ON `form_encounter` (`date`);
#EndIf

#IfNotIndex billing pid_encounter
DROP INDEX `pid` ON `billing`;
CREATE INDEX `pid_encounter` ON `billing` (`pid`, `encounter`);
#EndIf

#IfNotIndex transactions pid
CREATE INDEX `pid` ON `transactions` (`pid`);
#EndIf

#IfNotIndex pnotes pid
CREATE INDEX `pid` ON `pnotes` (`pid`);
#EndIf

#IfNotIndex procedure_order patient_id
CREATE INDEX `patient_id` ON `procedure_order` (`patient_id`);
#EndIf

#IfNotIndex immunizations patient_id
CREATE INDEX `patient_id` ON `immunizations` (`patient_id`);
#EndIf

#IfNotIndex lists type
CREATE INDEX `type` ON `lists` (`type`);
#EndIf

#IfNotIndex lists pid
CREATE INDEX `pid` ON `lists` (`pid`);
#EndIf

#IfNotRow2D list_options list_id lists option_id paymethod
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','paymethod','Payment Methods', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','Cash' ,'Cash' ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','Check','Check',20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','MC'   ,'MC'   ,30,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','VISA' ,'VISA' ,40,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','AMEX' ,'AMEX' ,50,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','DISC' ,'DISC' ,60,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('paymethod','Other','Other',70,0);
#EndIf

#IfNotIndex openemr_postcalendar_events pc_eventDate
CREATE INDEX `pc_eventDate` ON `openemr_postcalendar_events` (`pc_eventDate`);
#EndIf

#IfMissingColumn drugs dispensable
UPDATE drug_sales AS s, prescriptions AS p, form_encounter AS fe
  SET s.prescription_id = p.id WHERE
  s.pid > 0 AND
  s.encounter > 0 AND
  s.prescription_id = 0 AND
  fe.pid = s.pid AND
  fe.encounter = s.encounter AND
  p.patient_id = s.pid AND
  p.drug_id = s.drug_id AND
  p.start_date = fe.date;
ALTER TABLE drugs
  ADD dispensable tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = pharmacy elsewhere, 1 = dispensed here';
#EndIf

#IfMissingColumn ar_activity code_type
ALTER TABLE ar_activity
  ADD code_type varchar(15) NOT NULL DEFAULT '';
#EndIf

#IfMissingColumn drug_sales trans_type
ALTER TABLE drug_sales
  ADD trans_type tinyint NOT NULL DEFAULT 1 COMMENT '1=sale, 2=purchase, 3=return, 4=transfer, 5=adjustment';
UPDATE drug_sales SET trans_type = 4 WHERE pid = 0 AND xfer_inventory_id != 0;
UPDATE drug_sales SET trans_type = 5 WHERE trans_type = 1 AND pid = 0 AND fee = 0;
UPDATE drug_sales SET trans_type = 2 WHERE trans_type = 1 AND pid = 0 AND quantity >= 0;
UPDATE drug_sales SET trans_type = 3 WHERE trans_type = 1 AND pid = 0;
#EndIf

#IfMissingColumn ar_activity post_date
ALTER TABLE ar_activity
  ADD post_date date DEFAULT NULL COMMENT 'Posting date if specified at payment time';
UPDATE ar_activity SET post_date = post_time;
#EndIf

#IfMissingColumn drugs consumable
ALTER TABLE drugs
  ADD consumable tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = will not show on the fee sheet';
#EndIf

#IfMissingColumn facility extra_validation
ALTER TABLE facility ADD extra_validation tinyint(1) NOT NULL DEFAULT '1';
#EndIf

#IfMissingColumn form_encounter shift
ALTER TABLE form_encounter ADD shift varchar(31) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D list_options list_id lists option_id shift
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','shift','Shifts', 1,0);
#EndIf

#IfMissingColumn patient_data home_facility
ALTER TABLE patient_data
  ADD home_facility int(11) NOT NULL DEFAULT 0;
UPDATE patient_data AS pd SET pd.home_facility =
  (SELECT fe.facility_id FROM form_encounter AS fe WHERE fe.pid = pd.pid ORDER BY fe.date DESC LIMIT 1)
  WHERE pd.home_facility = 0 AND
  (SELECT count(*) FROM form_encounter AS fe2 WHERE fe2.pid = pd.pid) > 0;
#EndIf

#IfNotRow2D layout_options form_id DEM field_id home_facility
INSERT INTO `layout_options` (
  form_id, field_id, group_name, seq, title, data_type, uor, fld_length, max_length,
  list_id, titlecols, datacols, default_value, edit_options, description
  ) VALUES (
  'DEM', 'home_facility',
  (SELECT group_name AS gn FROM layout_options AS lo WHERE lo.form_id = 'DEM' AND lo.field_id = 'lname' LIMIT 1),
  0, 'Home Facility', 35, 2, 0, 0, '', 1, 1, '0', '', 'Default facility');
#EndIf

#IfNotTable voids
CREATE TABLE `voids` (
  `void_id`                bigint(20)    NOT NULL AUTO_INCREMENT,
  `patient_id`             bigint(20)    NOT NULL            COMMENT 'references patient_data.pid',
  `encounter_id`           bigint(20)    NOT NULL DEFAULT 0  COMMENT 'references form_encounter.encounter',
  `what_voided`            varchar(31)   NOT NULL            COMMENT 'checkout,receipt and maybe other options later',
  `date_original`          datetime      DEFAULT NULL        COMMENT 'time of original action that is now voided',
  `date_voided`            datetime      NOT NULL            COMMENT 'time of void action',
  `user_id`                bigint(20)    NOT NULL            COMMENT 'references users.id',
  `amount1`                decimal(12,2) NOT NULL DEFAULT 0  COMMENT 'for checkout,receipt total voided adjustments',
  `amount2`                decimal(12,2) NOT NULL DEFAULT 0  COMMENT 'for checkout,receipt total voided payments',
  `other_info`             text          NOT NULL DEFAULT '' COMMENT 'for checkout,receipt the old invoice refno',
  PRIMARY KEY (`void_id`),
  KEY datevoided (date_voided),
  KEY pidenc (patient_id, encounter_id)
) ENGINE=MyISAM;
#EndIf

#IfMissingColumn form_encounter billing_facility
ALTER TABLE form_encounter ADD billing_facility int(11) NOT NULL default 0;
UPDATE form_encounter SET billing_facility = facility_id;
#EndIf

#IfMissingColumn form_encounter voucher_number
ALTER TABLE form_encounter ADD voucher_number varchar(255) NOT NULL DEFAULT '' COMMENT 'also called referral number';
#EndIf

ALTER TABLE codes CHANGE code code varchar(31) NOT NULL default '';

