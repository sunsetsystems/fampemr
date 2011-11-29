DELETE FROM list_options WHERE list_id = 'ab_location';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('ab_location','proc' ,'Procedure at this site'              , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('ab_location','ma'   ,'Followup procedure from this site'   , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('ab_location','part' ,'Followup procedure from partner site', 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('ab_location','oth'  ,'Followup procedure from other site'  , 4);

DELETE FROM list_options WHERE list_id = 'lbfnames';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lbfnames','LBFgcac','IPPF GCAC',1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lbfnames','LBFsrh' ,'IPPF SRH' ,2);

DELETE FROM layout_options WHERE form_id = 'LBFsrh';
INSERT INTO layout_options VALUES ('LBFsrh','usertext15' ,'1Gynecology'                ,'Menstrual History'             , 1,22,1, 0, 0,'genmenhist'  ,1,3,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','men_hist'   ,'1Gynecology'                ,'Recent Menstrual History'      , 2,21,1, 2, 0,'menhist'     ,1,3,'','','Recent Menstrual History');
INSERT INTO layout_options VALUES ('LBFsrh','men_compl'  ,'1Gynecology'                ,'Menstrual Complications'       , 3,21,1, 2, 0,'men_compl'   ,1,3,'','','Menstrual Complications');
INSERT INTO layout_options VALUES ('LBFsrh','pap_hist'   ,'1Gynecology'                ,'Pap Smear Recent History'      , 4,22,1, 0, 0,'pap_hist'    ,1,3,'','','Pap Smear Recent History');
INSERT INTO layout_options VALUES ('LBFsrh','gyn_exams'  ,'1Gynecology'                ,'Gynecological Tests'           , 5,23,1, 0, 0,'gyn_exams'   ,1,1,'','','Gynecological test results');
INSERT INTO layout_options VALUES ('LBFsrh','pr_status'  ,'2Obstetrics'                ,'Pregnancy Status Confirmed'    , 1, 1,1, 0, 0,'pr_status'   ,1,3,'','','Pregnancy Status Confirmed');
INSERT INTO layout_options VALUES ('LBFsrh','gest_age_by','2Obstetrics'                ,'Gestational Age Confirmed By'  , 2, 1,1, 0, 0,'gest_age_by' ,1,3,'','','Gestational Age Confirmed By');
INSERT INTO layout_options VALUES ('LBFsrh','usertext12' ,'2Obstetrics'                ,'Blood Group'                   , 3, 1,1, 0, 0,'bloodgroup'  ,1,3,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','usertext13' ,'2Obstetrics'                ,'RH Factor'                     , 4, 1,1, 0, 0,'rh_factor'   ,1,3,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','obs_exams'  ,'2Obstetrics'                ,'Obstetric Tests'               , 5,23,1, 0, 0,'obs_exams'   ,1,1,'','','Obstetric test results');
INSERT INTO layout_options VALUES ('LBFsrh','usertext16' ,'2Obstetrics'                ,'Obstetric History'             , 6,22,1, 0, 0,'genobshist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','pr_outcome' ,'2Obstetrics'                ,'Outcome of Last Pregnancy'     , 7,21,1, 2, 0,'pr_outcome'  ,1,3,'','','Outcome of Last Pregnancy');
INSERT INTO layout_options VALUES ('LBFsrh','pr_compl'   ,'2Obstetrics'                ,'Pregnancy Complications'       , 8,21,1, 2, 0,'pr_compl'    ,1,3,'','','Pregnancy Complications');
INSERT INTO layout_options VALUES ('LBFsrh','usertext17' ,'3Basic RH (female only)'    ,'Abortion Basic History'        , 1,22,1, 0, 0,'genabohist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','abo_exams'  ,'3Basic RH (female only)'    ,'Abortion Tests'                , 2,23,1, 0, 0,'abo_exams'   ,1,1,'','','Abortion test results');
INSERT INTO layout_options VALUES ('LBFsrh','usertext18' ,'4Basic RH (female and male)','HIV/AIDS Basic History'        , 1,21,1, 0, 0,'genhivhist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','hiv_exams'  ,'4Basic RH (female and male)','HIV/AIDS Tests'                , 2,23,1, 0, 0,'hiv_exams'   ,1,1,'','','HIV/AIDS test results');
INSERT INTO layout_options VALUES ('LBFsrh','usertext19' ,'4Basic RH (female and male)','ITS/ITR Basic History'         , 3,21,1, 0, 0,'genitshist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','its_exams'  ,'4Basic RH (female and male)','ITS/ITR Tests'                 , 4,23,1, 0, 0,'its_exams'   ,1,1,'','','ITS/ITR test results');
INSERT INTO layout_options VALUES ('LBFsrh','usertext20' ,'4Basic RH (female and male)','Fertility Basic History'       , 5,21,1, 0, 0,'genferhist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','fer_exams'  ,'4Basic RH (female and male)','Fertility Tests'               , 6,23,1, 0, 0,'fer_exams'   ,1,1,'','','Infertility/subfertility test results');
INSERT INTO layout_options VALUES ('LBFsrh','fer_causes' ,'4Basic RH (female and male)','Causes of Infertility'         , 7,21,1, 2, 0,'fer_causes'  ,1,3,'','','Causes of Infertility');
INSERT INTO layout_options VALUES ('LBFsrh','fer_treat'  ,'4Basic RH (female and male)','Infertility Treatment'         , 8,21,1, 2, 0,'fer_treat'   ,1,3,'','','Infertility Treatment');
INSERT INTO layout_options VALUES ('LBFsrh','usertext21' ,'4Basic RH (female and male)','Urology Basic History'         , 9,21,1, 0, 0,'genurohist'  ,1,1,'','H','');
INSERT INTO layout_options VALUES ('LBFsrh','uro_exams'  ,'4Basic RH (female and male)','Urology Tests'                 ,10,23,1, 0, 0,'uro_exams'   ,1,1,'','','Urology test results');
INSERT INTO layout_options VALUES ('LBFsrh','uro_disease','4Basic RH (female and male)','Male Genitourinary diseases'   ,11,21,1, 2, 0,'uro_disease' ,1,3,'','','Male Genitourinary diseases');

DELETE FROM layout_options WHERE form_id = 'GCA';
INSERT INTO layout_options VALUES ('GCA','reason'       ,'2Counseling'  ,'Reason for Termination'          , 1,21,1, 0, 0,'abreasons'   ,1,3,'','' ,'Reasons for Termination of Pregnancy');
INSERT INTO layout_options VALUES ('GCA','exp_p_i'      ,'2Counseling'  ,'Explanation of Procedures/Issues', 2,21,1, 2, 0,'exp_p_i'     ,1,3,'','' ,'Explanation of Procedures and Issues');
INSERT INTO layout_options VALUES ('GCA','exp_pop'      ,'2Counseling'  ,'Explanation of Pregnancy Options', 3,21,1, 2, 0,'exp_pop'     ,1,3,'','' ,'Explanation of Pregnancy Options');
INSERT INTO layout_options VALUES ('GCA','ab_contraind' ,'2Counseling'  ,'Contraindications'               , 4,21,1, 2, 0,'ab_contraind',1,3,'','' ,'Contraindications');
INSERT INTO layout_options VALUES ('GCA','screening'    ,'2Counseling'  ,'Screening for SRHR Concerns'     , 5,21,1, 2, 0,'screening'   ,1,3,'','' ,'Screening for SRHR Concerns');
INSERT INTO layout_options VALUES ('GCA','in_ab_proc'   ,'3Admission'   ,'Induced Abortion Procedure'      , 2, 1,1, 0, 0,'in_ab_proc'  ,1,3,'','' ,'Abortion Procedure Accepted or Performed');
INSERT INTO layout_options VALUES ('GCA','ab_types'     ,'3Admission'   ,'Abortion Types'                  , 3,21,1, 2, 0,'ab_types'    ,1,3,'','' ,'Abortion Types');
INSERT INTO layout_options VALUES ('GCA','pr_status'    ,'4Preparatory' ,'Pregnancy Status Confirmed'      , 1, 1,1, 0, 0,'pr_status'   ,1,3,'','' ,'Pregnancy Status Confirmed');
INSERT INTO layout_options VALUES ('GCA','gest_age_by'  ,'4Preparatory' ,'Gestational Age Confirmed By'    , 2, 1,1, 0, 0,'gest_age_by' ,1,3,'','' ,'Gestational Age Confirmed By');
INSERT INTO layout_options VALUES ('GCA','usertext12'   ,'4Preparatory' ,'Blood Group'                     , 3, 1,1, 0, 0,'bloodgroup'  ,1,3,'','H','');
INSERT INTO layout_options VALUES ('GCA','usertext13'   ,'4Preparatory' ,'RH Factor'                       , 4, 1,1, 0, 0,'rh_factor'   ,1,3,'','H','');
INSERT INTO layout_options VALUES ('GCA','prep_procs'   ,'4Preparatory' ,'Preparation Procedures'          , 6,21,1, 0, 0,'prep_procs'  ,1,3,'','' ,'Preparation Procedures');
INSERT INTO layout_options VALUES ('GCA','pre_op'       ,'5Intervention','Pre-Surgery Procedures'          , 1,21,1, 2, 0,'pre_op'      ,1,3,'','' ,'Pre-Surgery Procedures');
INSERT INTO layout_options VALUES ('GCA','anesthesia'   ,'5Intervention','Anesthesia'                      , 2, 1,1, 0, 0,'anesthesia'  ,1,3,'','' ,'Type of Anesthesia Used');
INSERT INTO layout_options VALUES ('GCA','side_eff'     ,'5Intervention','Immediate Side Effects'          , 3,21,1, 2, 0,'side_eff'    ,1,3,'','' ,'Immediate Side Effects (observed at intervention');
INSERT INTO layout_options VALUES ('GCA','post_op'      ,'5Intervention','Post-Surgery Procedures'         , 5,21,1, 2, 0,'post_op'     ,1,3,'','' ,'Post-Surgery Procedures');
INSERT INTO layout_options VALUES ('GCA','qc_ind'       ,'6Followup'    ,'Quality of Care Indicators'      , 1,21,1, 0, 0,'qc_ind'      ,1,3,'','' ,'Quality of Care Indicators');

DELETE FROM layout_options WHERE form_id = 'LBFgcac';
INSERT INTO layout_options VALUES ('LBFgcac','client_status','1Basic Information','Client Status'               , 1,27,2, 0, 0,'clientstatus',1,1,'','' ,'Client Status');
INSERT INTO layout_options VALUES ('LBFgcac','ab_location'  ,'1Basic Information','Type of Visit'               , 2,27,2, 0, 0,'ab_location' ,1,1,'','' ,'Nature of this visit');
INSERT INTO layout_options VALUES ('LBFgcac','in_ab_proc'   ,'1Basic Information','Associated Induced Procedure', 3,27,1, 0, 0,'in_ab_proc'  ,1,3,'','' ,'Applies regardless of when or where done');
INSERT INTO layout_options VALUES ('LBFgcac','complications','2Complications','Complications'                   , 1,21,1, 2, 0,'complication',1,3,'','' ,'Post-Abortion Complications');
INSERT INTO layout_options VALUES ('LBFgcac','main_compl'   ,'2Complications','Main Complication'               , 2, 1,1, 2, 0,'complication',1,3,'','' ,'Primary Complication');
INSERT INTO layout_options VALUES ('LBFgcac','contrameth'   ,'3Contraception','New Method'                      , 1,21,1, 2, 0,'contrameth'  ,1,3,'','' ,'New method adopted');

#IfNotRow list_options list_id occupations
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('occupations','oth','Other', 1);
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'occupations';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','occupations','Occupations',61);
#EndIf

UPDATE layout_options SET data_type = 26, list_id = 'occupations'  WHERE form_id = 'DEM' AND field_id = 'occupation';
UPDATE layout_options SET data_type = 26, title = 'Religion'       WHERE form_id = 'DEM' AND field_id = 'userlist5';
UPDATE layout_options SET data_type = 26, title = 'Monthly Income' WHERE form_id = 'DEM' AND field_id = 'userlist3';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'ethnoracial';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'language';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'status';
# UPDATE layout_options SET uor = 0 WHERE form_id = 'DEM' AND field_id = 'providerID';

UPDATE layout_options SET seq = 3 WHERE form_id = 'REF' AND field_id = 'refer_external' AND seq = 5;
UPDATE layout_options SET seq = 4 WHERE form_id = 'REF' AND field_id = 'refer_to'       AND seq = 3;
UPDATE layout_options SET seq = 5 WHERE form_id = 'REF' AND field_id = 'body'           AND seq = 4;

# UPDATE layout_options AS a, list_options AS i SET a.group_name = '1Basic Data', a.title = 'Transgender', a.seq = 13, a.data_type = 26, a.uor = 1, a.description = 'Transgender', i.title = 'Transgender' WHERE a.form_id = 'DEM' AND a.field_id = 'userlist6' AND a.uor = 0 AND i.list_id = 'lists' AND i.option_id = 'userlist6';
UPDATE layout_options AS a, list_options AS i SET a.title = 'Transgender', a.data_type = 26, a.description = 'Transgender', i.title = 'Transgender' WHERE a.form_id = 'DEM' AND a.field_id = 'userlist6' AND a.uor = 0 AND i.list_id = 'lists' AND i.option_id = 'userlist6';

UPDATE list_options SET title = 'Retention of Product' WHERE title = 'Incomplete Abortion or Retention of Prod';

UPDATE list_options SET title = 'Surgical - MVA/EVA' WHERE list_id = 'in_ab_proc' AND title = 'Surgical - MVA';

DELETE FROM list_options where list_id = 'contrameth' AND option_id = 'abs';
DELETE FROM list_options where list_id = 'contrameth' AND option_id = 'eva';
DELETE FROM list_options where list_id = 'contrameth' AND option_id = 'oth';
DELETE FROM list_options where list_id = 'contrameth' AND option_id = 'wd';

UPDATE list_options SET mapping = ':2522231'   WHERE list_id = 'in_ab_proc' AND option_id = 's_dnc';
UPDATE list_options SET mapping = ':2522232'   WHERE list_id = 'in_ab_proc' AND option_id = 's_dne';
UPDATE list_options SET mapping = ':2522233'   WHERE list_id = 'in_ab_proc' AND option_id = 's_mva';
UPDATE list_options SET mapping = ':2522239'   WHERE list_id = 'in_ab_proc' AND option_id = 's_oth';
UPDATE list_options SET mapping = ':2522242'   WHERE list_id = 'in_ab_proc' AND option_id = 'm_mis';
UPDATE list_options SET mapping = ':2522241'   WHERE list_id = 'in_ab_proc' AND option_id = 'm_mm';
UPDATE list_options SET mapping = ':2522249'   WHERE list_id = 'in_ab_proc' AND option_id = 'm_oth';

UPDATE list_options SET mapping = ':11214'     WHERE list_id = 'contrameth' AND option_id = 'con';
UPDATE list_options SET mapping = ':11215'     WHERE list_id = 'contrameth' AND option_id = 'dia';
UPDATE list_options SET mapping = ':14521'     WHERE list_id = 'contrameth' AND option_id = 'ec';
UPDATE list_options SET mapping = ':13119'     WHERE list_id = 'contrameth' AND option_id = 'fab';
UPDATE list_options SET mapping = ':11216'     WHERE list_id = 'contrameth' AND option_id = 'fc';
UPDATE list_options SET mapping = ':11113'     WHERE list_id = 'contrameth' AND option_id = 'pat';
UPDATE list_options SET mapping = ':11112'     WHERE list_id = 'contrameth' AND option_id = 'imp';
UPDATE list_options SET mapping = ':11111'     WHERE list_id = 'contrameth' AND option_id = 'inj';
UPDATE list_options SET mapping = ':11317'     WHERE list_id = 'contrameth' AND option_id = 'iud';
UPDATE list_options SET mapping = ':11110'     WHERE list_id = 'contrameth' AND option_id = 'or';
UPDATE list_options SET mapping = ':11215'     WHERE list_id = 'contrameth' AND option_id = 'cap';
UPDATE list_options SET mapping = ':11216'     WHERE list_id = 'contrameth' AND option_id = 'sp';
UPDATE list_options SET mapping = ':12.18'     WHERE list_id = 'contrameth' AND option_id = 'vsc';
UPDATE list_options SET mapping = ':00000'     WHERE list_id = 'contrameth' AND option_id = 'no';

UPDATE list_options SET mapping = 'F' WHERE list_id = 'sex' AND option_id = 'Female';
UPDATE list_options SET mapping = 'M' WHERE list_id = 'sex' AND option_id = 'Male';

#IfNotRow2D list_options list_id userlist2 mapping 1
UPDATE list_options SET title = 'Education' WHERE list_id = 'lists' AND option_id = 'userlist2';
DELETE FROM list_options WHERE list_id = 'userlist2';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','1','Illiterate',1,0,'0');
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','2','Basic Schooling',2,1,'1');
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','3','Advanced Schooling',3,0,'2');
#EndIf

#IfNotRow lang_constants constant_name (New Patient)
insert into lang_constants ( constant_name ) values ( '(New Patient)' );
#EndIf
#IfNotRow lang_constants constant_name Active Encounter
insert into lang_constants ( constant_name ) values ( 'Active Encounter' );
#EndIf
#IfNotRow lang_constants constant_name Active Patient
insert into lang_constants ( constant_name ) values ( 'Active Patient' );
#EndIf
#IfNotRow lang_constants constant_name Active Patient:
insert into lang_constants ( constant_name ) values ( 'Active Patient:' );
#EndIf
#IfNotRow lang_constants constant_name Add Copay
insert into lang_constants ( constant_name ) values ( 'Add Copay' );
#EndIf
#IfNotRow lang_constants constant_name Add Drug
insert into lang_constants ( constant_name ) values ( 'Add Drug' );
#EndIf
#IfNotRow lang_constants constant_name Add Patient Record
insert into lang_constants ( constant_name ) values ( 'Add Patient Record' );
#EndIf
#IfNotRow lang_constants constant_name Appointments and Encounters
insert into lang_constants ( constant_name ) values ( 'Appointments and Encounters' );
#EndIf
#IfNotRow lang_constants constant_name Appointments and Visits
insert into lang_constants ( constant_name ) values ( 'Appointments and Visits' );
#EndIf
#IfNotRow lang_constants constant_name Appt-Enc
insert into lang_constants ( constant_name ) values ( 'Appt-Enc' );
#EndIf
#IfNotRow lang_constants constant_name Cash Rec
insert into lang_constants ( constant_name ) values ( 'Cash Rec' );
#EndIf
#IfNotRow lang_constants constant_name Cash Receipts
insert into lang_constants ( constant_name ) values ( 'Cash Receipts' );
#EndIf
#IfNotRow lang_constants constant_name Chart
insert into lang_constants ( constant_name ) values ( 'Chart' );
#EndIf
#IfNotRow lang_constants constant_name Chief Complaint
insert into lang_constants ( constant_name ) values ( 'Chief Complaint' );
#EndIf
#IfNotRow lang_constants constant_name Chief Compliant
insert into lang_constants ( constant_name ) values ( 'Chief Compliant' );
#EndIf
#IfNotRow lang_constants constant_name Client
insert into lang_constants ( constant_name ) values ( 'Client' );
#EndIf
#IfNotRow lang_constants constant_name CO-PAY
insert into lang_constants ( constant_name ) values ( 'CO-PAY' );
#EndIf
#IfNotRow lang_constants constant_name Collections
insert into lang_constants ( constant_name ) values ( 'Collections' );
#EndIf
#IfNotRow lang_constants constant_name COPAY
insert into lang_constants ( constant_name ) values ( 'COPAY' );
#EndIf
#IfNotRow lang_constants constant_name CPT4
insert into lang_constants ( constant_name ) values ( 'CPT4' );
#EndIf
#IfNotRow lang_constants constant_name Encounter
insert into lang_constants ( constant_name ) values ( 'Encounter' );
#EndIf
#IfNotRow lang_constants constant_name Encounter Forms to Include in this Report:
insert into lang_constants ( constant_name ) values ( 'Encounter Forms to Include in this Report:' );
#EndIf
#IfNotRow lang_constants constant_name Encounters
insert into lang_constants ( constant_name ) values ( 'Encounters' );
#EndIf
#IfNotRow lang_constants constant_name Encounters Report
insert into lang_constants ( constant_name ) values ( 'Encounters Report' );
#EndIf
#IfNotRow lang_constants constant_name Facility
insert into lang_constants ( constant_name ) values ( 'Facility' );
#EndIf
#IfNotRow lang_constants constant_name Fee Sheet
insert into lang_constants ( constant_name ) values ( 'Fee Sheet' );
#EndIf
#IfNotRow lang_constants constant_name Find Patient
insert into lang_constants ( constant_name ) values ( 'Find Patient' );
#EndIf
#IfNotRow lang_constants constant_name ID
insert into lang_constants ( constant_name ) values ( 'ID' );
#EndIf
#IfNotRow lang_constants constant_name IPPF SRH Data
insert into lang_constants ( constant_name ) values ( 'IPPF SRH Data' );
#EndIf
#IfNotRow lang_constants constant_name IPPF SRH Data for
insert into lang_constants ( constant_name ) values ( 'IPPF SRH Data for' );
#EndIf
#IfNotRow lang_constants constant_name Last
insert into lang_constants ( constant_name ) values ( 'Last' );
#EndIf
#IfNotRow lang_constants constant_name New Encounter
insert into lang_constants ( constant_name ) values ( 'New Encounter' );
#EndIf
#IfNotRow lang_constants constant_name New Encounter Form
insert into lang_constants ( constant_name ) values ( 'New Encounter Form' );
#EndIf
#IfNotRow lang_constants constant_name New Patient
insert into lang_constants ( constant_name ) values ( 'New Patient' );
#EndIf
#IfNotRow lang_constants constant_name Past Encounters and Documents
insert into lang_constants ( constant_name ) values ( 'Past Encounters and Documents' );
#EndIf
#IfNotRow lang_constants constant_name Patient
insert into lang_constants ( constant_name ) values ( 'Patient' );
#EndIf
#IfNotRow lang_constants constant_name Patient Appointment
insert into lang_constants ( constant_name ) values ( 'Patient Appointment' );
#EndIf
#IfNotRow lang_constants constant_name Patient Encounter Form
insert into lang_constants ( constant_name ) values ( 'Patient Encounter Form' );
#EndIf
#IfNotRow lang_constants constant_name Patient Notes
insert into lang_constants ( constant_name ) values ( 'Patient Notes' );
#EndIf
#IfNotRow lang_constants constant_name Patient Number
insert into lang_constants ( constant_name ) values ( 'Patient Number' );
#EndIf
#IfNotRow lang_constants constant_name Patient Record Report
insert into lang_constants ( constant_name ) values ( 'Patient Record Report' );
#EndIf
#IfNotRow lang_constants constant_name Patient Report
insert into lang_constants ( constant_name ) values ( 'Patient Report' );
#EndIf
#IfNotRow lang_constants constant_name Patient Transactions
insert into lang_constants ( constant_name ) values ( 'Patient Transactions' );
#EndIf
#IfNotRow lang_constants constant_name Patient/Client
insert into lang_constants ( constant_name ) values ( 'Patient/Client' );
#EndIf
#IfNotRow lang_constants constant_name PID
insert into lang_constants ( constant_name ) values ( 'PID' );
#EndIf
#IfNotRow lang_constants constant_name Prepay
insert into lang_constants ( constant_name ) values ( 'Prepay' );
#EndIf
#IfNotRow lang_constants constant_name Provider
insert into lang_constants ( constant_name ) values ( 'Provider' );
#EndIf
#IfNotRow lang_constants constant_name Pt Notes/Auth
insert into lang_constants ( constant_name ) values ( 'Pt Notes/Auth' );
#EndIf
#IfNotRow lang_constants constant_name Rendering
insert into lang_constants ( constant_name ) values ( 'Rendering' );
#EndIf
#IfNotRow lang_constants constant_name Rx
insert into lang_constants ( constant_name ) values ( 'Rx' );
#EndIf
#IfNotRow lang_constants constant_name Sales by Item
insert into lang_constants ( constant_name ) values ( 'Sales by Item' );
#EndIf
#IfNotRow lang_constants constant_name Save Patient Demographic
insert into lang_constants ( constant_name ) values ( 'Save Patient Demographic' );
#EndIf
#IfNotRow lang_constants constant_name Search or Add Patient
insert into lang_constants ( constant_name ) values ( 'Search or Add Patient ' );
#EndIf
#IfNotRow lang_constants constant_name Select Patient
insert into lang_constants ( constant_name ) values ( 'Select Patient' );
#EndIf
#IfNotRow lang_constants constant_name Select Patient by Last
insert into lang_constants ( constant_name ) values ( 'Select Patient by Last' );
#EndIf
#IfNotRow lang_constants constant_name Sensitivity
insert into lang_constants ( constant_name ) values ( 'Sensitivity' );
#EndIf
#IfNotRow lang_constants constant_name SSN
insert into lang_constants ( constant_name ) values ( 'SSN' );
#EndIf
#IfNotRow lang_constants constant_name Superbill
insert into lang_constants ( constant_name ) values ( 'Superbill' );
#EndIf
#IfNotRow lang_constants constant_name This Encounter
insert into lang_constants ( constant_name ) values ( 'This Encounter' );
#EndIf
#IfNotRow lang_constants constant_name Transact
insert into lang_constants ( constant_name ) values ( 'Transact' );
#EndIf
#IfNotRow lang_constants constant_name User & Group Administration
insert into lang_constants ( constant_name ) values ( 'User & Group Administration' );
#EndIf
#IfNotRow lang_constants constant_name View Comprehensive Patient Report
insert into lang_constants ( constant_name ) values ( 'View Comprehensive Patient Report' );
#EndIf
#IfNotRow lang_constants constant_name Procedures
insert into lang_constants ( constant_name ) values ( 'Procedures' );
#EndIf
#IfNotRow lang_constants constant_name Layout Based
insert into lang_constants ( constant_name ) values ( 'Layout Based' );
#EndIf

insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'New Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = '(New Patient)' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Active visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Active Encounter' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Active Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Active Patient' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Active Client:' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Active Patient:' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Add Payment' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Add Copay' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Add Product' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Add Drug' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Create a new OpenEMR record' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Add Patient Record' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Appointments and Visits' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Appointments and Encounters' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Charges by Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Appointments and Visits' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Appt-Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Appt-Enc' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Svcs Provider Cash Rec' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Cash Rec' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Service Provider Cash Receipts' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Cash Receipts' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'System ID' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Chart' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Reason of Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Chief Complaint' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Reason of Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Chief Compliant' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Cliente' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Client' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Payment' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'CO-PAY' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Collection Report' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Collections' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Payment' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'COPAY' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'C3' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'CPT4' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Encounter' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Visit Forms to include in this Report:' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Encounter Forms to Include in this Report:' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Visits' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Encounters' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Visits Report' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Encounters Report' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Clinic ID' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Facility' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Tally Sheet' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Fee Sheet' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Find Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Find Patient' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'ID' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'ID' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'GCAC Form (example)' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'IPPF SRH Data' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'GCAC Form (example) for' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'IPPF SRH Data for' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Last name' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Last' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'New Encounter' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'New Visit Form' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'New Encounter Form' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'New Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'New Patient' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Past Visits and Documents' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Past Encounters and Documents' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client Appointment' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Appointment' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client Visit Form' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Encounter Form' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client Notes' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Notes' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client number' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Number' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client Record Report' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Record Report' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client Report' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Report' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Referrals and Other Transactions' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient Transactions' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Patient/Client' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client ID (assign by the system)' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'PID' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Payments' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Prepay' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Service Provider' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Provider' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Client notes/Auth' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Pt Notes/Auth' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Default' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Rendering' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Prescp & Disp' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Rx' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Sales by Item' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Sales by Item' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Save Demographic Client  Data' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Save Patient Demographic' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Search or Add Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Search or Add Patient ' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Select Client' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Select Patient' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Select Client by Last' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Select Patient by Last' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Risk' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Sensitivity' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'National ID' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'SSN' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Tally Sheet' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Superbill' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'This Visit' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'This Encounter' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Referrals' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Transact' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'User Administration' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'User & Group Administration' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'View Comprehensive Client Report' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'View Comprehensive Patient Report' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Diagnostic Labs & Orders' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Procedures' and ld.cons_id is null;
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'Program' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'Layout Based' and ld.cons_id is null;

UPDATE openemr_postcalendar_categories SET pc_catname = '1 Admission', pc_catcolor = '#FFFFFF' WHERE pc_catid = 10 AND pc_catname = 'New Patient';
UPDATE openemr_postcalendar_categories SET pc_catname = '2 Re-Visit', pc_catcolor = '#CCFFFF' WHERE pc_catid = 9 AND pc_catname = 'Established Patient';
#IfNotRow openemr_postcalendar_categories pc_catid 12
INSERT INTO `openemr_postcalendar_categories` VALUES (12,'3 Counselling Only','#FFFFCC','Counselling',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);
#EndIf
#IfNotRow openemr_postcalendar_categories pc_catid 13
INSERT INTO `openemr_postcalendar_categories` VALUES (13,'4 Supply/Re-Supply','#CCCCCC','Supply/Re-Supply',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);
#EndIf
#IfNotRow openemr_postcalendar_categories pc_catid 14
INSERT INTO `openemr_postcalendar_categories` VALUES (14,'5 Administrative','#FFFFFF','Supply/Re-Supply',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);
#EndIf

#IfNotRow globals gl_name full_new_patient_form
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'full_new_patient_form'       , 0, '3' );
#EndIf
#IfNotRow globals gl_name patient_search_results_style
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'patient_search_results_style', 0, '1' );
#EndIf
#IfNotRow globals gl_name simplified_demographics
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'simplified_demographics'     , 0, '1' );
#EndIf
#IfNotRow globals gl_name online_support_link
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'online_support_link'         , 0, ''  );
#EndIf
#IfNotRow globals gl_name units_of_measurement
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'units_of_measurement'        , 0, '2' );
#EndIf
#IfNotRow globals gl_name specific_application
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'specific_application'        , 0, '2' );
#EndIf
#IfNotRow globals gl_name inhouse_pharmacy
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'inhouse_pharmacy'            , 0, '2' );
#EndIf
#IfNotRow globals gl_name configuration_import_export
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'configuration_import_export' , 0, '1' );
#EndIf

#IfNotRow code_types ct_id 11
DELETE FROM code_types;
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('MA'  ,12, 1, 0, '', 1, 1, 0, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('IPPF',11, 2, 0, '', 0, 0, 1, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('ICD9', 2, 3, 2, '', 0, 0, 0, 1);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('ACCT',13, 4, 0, '', 0, 0, 1, 0);
#EndIf

#IfNotRow2D layout_options form_id LBFgcac field_id gc_rreason
DELETE FROM layout_options WHERE form_id = 'LBFgcac';
INSERT INTO layout_options VALUES ('LBFgcac','client_status','1Basic Information','Client Status'               , 1,27,2, 0, 0,'clientstatus',1,1,'','' ,'Client Status');
INSERT INTO layout_options VALUES ('LBFgcac','ab_location'  ,'1Basic Information','Type of Visit'               , 2,27,2, 0, 0,'ab_location' ,1,1,'','' ,'Nature of this visit');
INSERT INTO layout_options VALUES ('LBFgcac','in_ab_proc'   ,'1Basic Information','Associated Induced Procedure', 3,27,1, 0, 0,'in_ab_proc'  ,1,3,'','' ,'Applies regardless of when or where done');
INSERT INTO layout_options VALUES ('LBFgcac','complications','2Complications','Complications'                   , 1,21,1, 2, 0,'complication',1,3,'','' ,'Post-Abortion Complications');
INSERT INTO layout_options VALUES ('LBFgcac','main_compl'   ,'2Complications','Main Complication'               , 2, 1,1, 2, 0,'complication',1,3,'','' ,'Primary Complication');
INSERT INTO layout_options VALUES ('LBFgcac','gc_rreason'   ,'3IPPA CAC Section','Reason when Rejected/Referred', 1, 1,0, 0, 0,'gc_rreason'   ,1,3,'','','Reason for rejecting or referring services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_reason'    ,'3IPPA CAC Section','Main Reason for MR Services'  , 1, 1,0, 0, 0,'gc_reason'   ,1,3,'','' ,'Main reason for requesting MR services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_condition' ,'3IPPA CAC Section','Aborted Conception Condition' , 2, 1,0, 0 ,0,'gc_condition',1,3,'','' ,'Condition of Aborted Conception');
INSERT INTO layout_options VALUES ('LBFgcac','gc_efforts'   ,'3IPPA CAC Section','Efforts Prior to Visit'       , 3, 1,0, 0, 0,'gc_efforts'  ,1,3,'','' ,'Other efforts conducted before visiting the clinic');
INSERT INTO layout_options VALUES ('LBFgcac','gc_complaint' ,'3IPPA CAC Section','Complaint from Client'        , 4, 1,0, 0, 0,'gc_complaint',1,3,'','' ,'Complaint from Client');
#EndIf

# #IfNotRow2D list_options list_id lists option_id gc_rreason
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_rreason','GCAC Reason to Reject/Refer Services',88);
# DELETE FROM list_options WHERE list_id = 'gc_rreason';
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','1' ,'Service not available', 1);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','2' ,'Cost of service'      , 2);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','3' ,'Single'               , 3);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','4' ,'Medical reason'       , 4);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','5' ,'No responsible person', 5);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','6' ,'Weeks of pregnancy'   , 6);
# #EndIf

# #IfNotRow2D list_options list_id lists option_id gc_reason
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_reason','GCAC Main Reason for MR Services',88);
# DELETE FROM list_options WHERE list_id = 'gc_reason';
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','1' ,'Have already enough children'                , 1);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','2' ,'The children are still babies'               , 2);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','3' ,'Too young to have baby'                      , 3);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','4' ,'Too old to have other child'                 , 4);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','5' ,'Not / not yet married'                       , 5);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','6' ,'Still goes to school / college'              , 6);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','7' ,'Engage with Official'                        , 7);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','8' ,'Could not stand pain / sickness of pregnancy', 8);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','9' ,'Others'                                      , 9);
# #EndIf

# #IfNotRow2D list_options list_id lists option_id gc_condition
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_condition','GCAC Aborted Conception Condition',88);
# DELETE FROM list_options WHERE list_id = 'gc_condition';
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','1' ,'Fresh' , 1);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','2' ,'Dark'  , 2);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','3' ,'Sticky', 3);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','4' ,'Others', 4);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','5' ,'N/A'   , 0);
# #EndIf

# #IfNotRow2D list_options list_id lists option_id gc_efforts
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_efforts','GCAC Prior Efforts',88);
# DELETE FROM list_options WHERE list_id = 'gc_efforts';
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','1' ,'None'                                         , 1);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','2' ,'Drinking herbs / medicines'                   , 2);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','3' ,'Had been taken care by paramedic'             , 3);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','4' ,'Massage / went to traditional birth attendant', 4);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','5' ,'Other efforts'                                , 5);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','6' ,'Emergency'                                    , 6);
# #EndIf

# #IfNotRow2D list_options list_id lists option_id gc_complaint
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_complaint','GCAC Complaint from Client',88);
# DELETE FROM list_options WHERE list_id = 'gc_complaint';
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','1' ,'Facility'         , 1);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','2' ,'Time of Queue'    , 2);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','3' ,'Charge of Service', 3);
# INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','4' ,'None'             , 4);
# #EndIf

#IfNotRow2D list_options list_id clientstatus option_id defer
DELETE FROM list_options WHERE list_id = 'clientstatus';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('clientstatus','maaa'  ,'MA Client Accepting Abortion', 1,1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('clientstatus','mara'  ,'MA Client Refusing Abortion' , 2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('clientstatus','refin' ,'Inbound Referral'            , 3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('clientstatus','self'  ,'Self Referred'               , 4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('clientstatus','defer' ,'Deferring / Undecided'       , 5,0,0);
#EndIf

#IfNotRow2D list_options list_id ab_location option_id na
DELETE FROM list_options WHERE list_id = 'ab_location';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('ab_location','proc' ,'Procedure at this site'              , 1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('ab_location','ma'   ,'Followup procedure from this site'   , 2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('ab_location','part' ,'Followup procedure from partner site', 3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('ab_location','oth'  ,'Followup procedure from other site'  , 4,0,0);
#EndIf

#IfNotRow2D layout_options form_id REF field_id reply_related_code
DELETE FROM layout_options WHERE form_id = 'REF';
INSERT INTO layout_options VALUES ('REF','refer_date'        ,'1Referral','Referral Date'                  , 5, 4,2, 0,  0,''         ,1,1,'C','D','Date of referral');
INSERT INTO layout_options VALUES ('REF','refer_from'        ,'1Referral','Referred By'                    ,10,14,2, 0,  0,''         ,1,1,'' ,'' ,'Referral By');
INSERT INTO layout_options VALUES ('REF','refer_external'    ,'1Referral','Referral Type'                  ,15, 1,2, 0,  0,'reftype'  ,1,1,'' ,'' ,'Type of referral');
INSERT INTO layout_options VALUES ('REF','refer_to'          ,'1Referral','Referred To'                    ,20,14,2, 0,  0,''         ,1,1,'' ,'' ,'Referral To');
INSERT INTO layout_options VALUES ('REF','body'              ,'1Referral','Reason'                         ,25, 3,2,30,  3,''         ,1,1,'' ,'' ,'Reason for referral');
INSERT INTO layout_options VALUES ('REF','refer_risk_level'  ,'1Referral','Risk Level'                     ,30, 1,1, 0,  0,'risklevel',1,1,'' ,'' ,'Level of urgency');
INSERT INTO layout_options VALUES ('REF','refer_vitals'      ,'1Referral','Include Vital Signs'            ,35, 1,1, 0,  0,'boolean'  ,1,1,'' ,'' ,'Include vitals data?');
INSERT INTO layout_options VALUES ('REF','refer_reply_date'  ,'1Referral','Expected Reply Date'            ,40, 4,2, 0,  0,''         ,1,1,'' ,'D','Expected date of reply');
INSERT INTO layout_options VALUES ('REF','refer_related_code','1Referral','Requested Service'              ,45,15,2,30,255,''         ,1,1,'' ,'' ,'Billing Code for Requested Service');
INSERT INTO layout_options VALUES ('REF','refer_diag'        ,'1Referral','Preliminary Diagnosis'          ,50, 2,1,30,255,''         ,1,1,'' ,'X','Referrer diagnosis');
INSERT INTO layout_options VALUES ('REF','reply_date'        ,'2Counter-Referral','Reply Date'             , 5, 4,1, 0,  0,''         ,1,1,'' ,'D','Date of reply');
INSERT INTO layout_options VALUES ('REF','reply_from'        ,'2Counter-Referral','Reply From'             ,10, 2,1,30,255,''         ,1,1,'' ,'' ,'Who replied?');
INSERT INTO layout_options VALUES ('REF','reply_init_diag'   ,'2Counter-Referral','Presumed Diagnosis'     ,15, 2,0,30,255,''         ,1,1,'' ,'' ,'Presumed diagnosis by specialist');
INSERT INTO layout_options VALUES ('REF','reply_final_diag'  ,'2Counter-Referral','Final Diagnosis'        ,20, 2,1,30,255,''         ,1,1,'' ,'' ,'Final diagnosis by specialist');
INSERT INTO layout_options VALUES ('REF','reply_documents'   ,'2Counter-Referral','Documents'              ,25, 2,1,30,255,''         ,1,1,'' ,'' ,'Where may related scanned or paper documents be found?');
INSERT INTO layout_options VALUES ('REF','reply_findings'    ,'2Counter-Referral','Findings'               ,30, 3,1,30,  3,''         ,1,1,'' ,'' ,'Findings by specialist');
INSERT INTO layout_options VALUES ('REF','reply_services'    ,'2Counter-Referral','Services Provided'      ,35, 3,0,30,  3,''         ,1,1,'' ,'' ,'Service provided by specialist');
INSERT INTO layout_options VALUES ('REF','reply_related_code','2Counter-Referral','Service Provided'       ,40,15,1,30,255,''         ,1,1,'' ,'' ,'Billing Code for actual services provided');
INSERT INTO layout_options VALUES ('REF','reply_recommend'   ,'2Counter-Referral','Recommendations'        ,45, 3,1,30,  3,''         ,1,1,'' ,'' ,'Recommendations by specialist');
INSERT INTO layout_options VALUES ('REF','reply_rx_refer'    ,'2Counter-Referral','Prescriptions/Referrals',50, 3,1,30,  3,''         ,1,1,'' ,'' ,'Prescriptions and/or referrals by specialist');
#EndIf

#IfMissingColumn patient_data usertext11
ALTER TABLE patient_data
  ADD usertext11 varchar(255) NOT NULL DEFAULT '',
  ADD usertext12 varchar(255) NOT NULL DEFAULT '',
  ADD usertext13 varchar(255) NOT NULL DEFAULT '',
  ADD usertext14 varchar(255) NOT NULL DEFAULT '',
  ADD usertext15 varchar(255) NOT NULL DEFAULT '',
  ADD usertext16 varchar(255) NOT NULL DEFAULT '',
  ADD usertext17 varchar(255) NOT NULL DEFAULT '',
  ADD usertext18 varchar(255) NOT NULL DEFAULT '',
  ADD usertext19 varchar(255) NOT NULL DEFAULT '',
  ADD usertext20 varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D layout_options form_id DEM field_id usertext11
INSERT INTO `layout_options` VALUES ('DEM', 'usertext11', '6Misc', 'User Defined Text 11', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext12', '6Misc', 'User Defined Text 12', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext13', '6Misc', 'User Defined Text 13', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext14', '6Misc', 'User Defined Text 14', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext15', '6Misc', 'User Defined Text 15', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext16', '6Misc', 'User Defined Text 16', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext17', '6Misc', 'User Defined Text 17', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext18', '6Misc', 'User Defined Text 18', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext19', '6Misc', 'User Defined Text 19', 8,2,0,10,63,'',1,1,'','','User Defined');
INSERT INTO `layout_options` VALUES ('DEM', 'usertext20', '6Misc', 'User Defined Text 20', 8,2,0,10,63,'',1,1,'','','User Defined');
#EndIf

#IfNotRow code_types ct_key REF
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag) VALUES ('REF',16, 5, 0, '', 0, 1, 1, 0);
#EndIf

#IfNotRow2D list_options list_id lists option_id actorest
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','actorest','Actual or Estimated', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('actorest','act'  ,'Actual'   ,10,1);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('actorest','est'  ,'Estimated',20,0);
UPDATE layout_options SET group_name = '1Who', title='', seq = 7, data_type = 1,
  uor = 1, fld_length = 0, list_id = 'actorest', titlecols = 0, datacols = 0,
  description = 'Indicates if DOB is estimated' WHERE
  form_id = 'DEM' AND field_id = 'usertext3' AND uor = 0;
#EndIf

#IfNotRow lang_constants constant_name CLIA Number
insert into lang_constants ( constant_name ) values ( 'CLIA Number' );
#EndIf
insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'SDP ID' from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'CLIA Number' and ld.cons_id is null;

# 2011-08-01 we decided not to do this.
# 2011-08-10 decided to do it again.
UPDATE facility SET domain_identifier = facility_npi WHERE facility_npi != '' AND ( domain_identifier IS NULL OR domain_identifier = '' );

# The following re-added 2011-08-15 because LV asked for it. --Rod
#IfNotRow2D list_options list_id lists option_id posref
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','posref','Channels of Distribution', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','01','Static Clinic'         ,01,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','02','Mobile/Outreach Clinic',02,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','03','Associated Clinics'    ,03,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','04','Private Physicians'    ,04,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','05','CBD / CBS'             ,05,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','06','MA Social Marketing'   ,06,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','07','Commercial Marketing'  ,07,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','08','Government'            ,08,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('posref','09','Other Agencies'        ,09,0);
#EndIf
#IfNotRow lang_constants constant_name POS Code
INSERT INTO lang_constants ( constant_name ) VALUES ( 'POS Code' );
#EndIf
INSERT INTO lang_definitions ( cons_id, lang_id, definition ) SELECT lc.cons_id, 1, 'COD Code' FROM lang_constants AS lc LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND ld.lang_id = 1 WHERE lc.constant_name = 'POS Code' AND ld.cons_id IS NULL;

#IfNotRow2D list_options list_id lists option_id ippfconmeth
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','ippfconmeth','IPPF Contraceptive Methods', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111101110','COC & POC'                                   ,01,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111111110','Combined Injectable Contraceptives (1 month)',02,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111112110','Progestogen Only Injectables (2 months)'     ,03,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111113110','Progestogen Only Injectables (3 months)'     ,04,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111122110','Subdermal implants 6 rods'                   ,05,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111123110','Subdermal implants 2 rods'                   ,06,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111124110','Subdermal implants 1 rod'                    ,07,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111132110','Transdermal Patch (1 month)'                 ,08,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','111133110','Vaginal Ring (1 month)'                      ,09,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112141110','Male Condom'                                 ,10,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112142110','Female Condom'                               ,11,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112151110','Diaphragm'                                   ,12,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112152010','Cervical Cap'                                ,13,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112161110','Spermicides - Foam Tabs/Tube/Suppositories'  ,14,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112162110','Spermicides - Foam Tabs/Strip'               ,15,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112163110','Spermicides - Foam Cans'                     ,16,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112164110','Spermicides - Cream & Jelly'                 ,17,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','112165110','Spermicides - Pessaries / C-film'            ,18,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','113171110','Hormone releasing IUD (5 years)'             ,19,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','113172110','Copper releasing IUD (10 years) '            ,20,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','121181213','Female VSC - Minilaparatomy'                 ,21,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','121181313','Female VSC - Laparoscopy'                    ,22,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','121181413','Female VSC - Laparotomy'                     ,23,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','122182213','Male VSC - Incisional vasectomy'             ,24,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('ippfconmeth','122182313','Male VSC - No-scalpel Vasectomy'             ,25,0);
#EndIf

#IfNotRow2D list_options list_id lists option_id contratype
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','contratype','Contraception Event Types', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('contratype','1','Starting for lifetime but not MA' ,1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('contratype','2','Starting for MA but not lifetime' ,2,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('contratype','3','Starting for both lifetime and MA',3,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('contratype','4','Change of method, not starting'   ,4,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('contratype','5','Not choosing contraception'       ,5,0);
#EndIf

#IfNotRow2D codes code_type 11 code 252221329
DELETE FROM codes WHERE code_type = '11';
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Oral Contraceptives - OC - Method Specific Counselling', '111100119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Oral Contraceptives - OC - OTHER', '111100999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Oral Contraceptives - COC & POC - Initial Consultation', '111101110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Oral Contraceptives - COC & POC - Follow up/Resupply', '111101111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Oral Contraceptives - COC & POC - OTHER', '111101999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Injectable Contraceptives - Method Specific Counselling', '111110119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Injectable Contraceptives - OTHER', '111110999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Combined Injectable Contraceptives (1 month) -  Initial Consultation', '111111110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Combined Injectable Contraceptives (1 month) - Follow up/Resupply', '111111111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Combined Injectable Contraceptives (1 month) - OTHER', '111111999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (2 months) - Initial Consultation', '111112110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (2 months) - Follow up/Resupply', '111112111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (2 months) - OTHER', '111112999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (3 months) - Initial Consultation', '111113110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (3 months) - Follow up/Resupply', '111113111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Progestogen Only Injectables (3 months) - OTHER', '111113999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal Implants - Removal', '111120112', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal Implants - Method Specific Counselling', '111120119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal Implants - OTHER', '111120999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 6 rods - Initial Consultation/Insertion', '111122110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 6 rods - Follow up/Reinsertion', '111122111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 6 rods - OTHER', '111122999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 2 rods - Initial Consultation/Insertion', '111123110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 2 rods - Follow up/Reinsertion', '111123111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 2 rods - OTHER', '111123999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 1 rod - Initial Consultation/Insertion', '111124110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 1 rod - Follow up/Reinsertion', '111124111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Subdermal implants 1 rod - OTHER', '111124999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other hormonal methods - OTHER', '111130999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Transdermal Patch (1 month) - Initial Consultation', '111132110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Transdermal Patch (1 month) - Follow up/Resupply', '111132111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Transdermal Patch (1 month) - Method Specific Counselling', '111132119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Transdermal Patch (1 month) - OTHER', '111132999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Vaginal Ring (1 month) - Initial Consultation', '111133110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Vaginal Ring (1 month) - Follow up/Resupply', '111133111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Vaginal Ring (1 month) - Method Specific Counselling', '111133119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Other methods - Vaginal Ring (1 month) - OTHER', '111133999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms (Male and Female) - OTHER', '112140999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Male Condom - Initial Consultation', '112141110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Male Condom - Follow up/Resupply', '112141111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Male Condom - Method Specific Counselling', '112141119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Male Condom - OTHER', '112141999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Female Condom - Initial Consultation', '112142110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Female Condom - Follow up/Resupply', '112142111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Female Condom - Method Specific Counselling', '112142119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Condoms - Female Condom - OTHER', '112142999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Diaphragm / Cervical Cap - Method Specific Counselling', '112150119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Diaphragm / Cervical Cap - OTHER', '112150999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Diaphragm - Initial Consultation', '112151110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Diaphragm - Follow up/Resupply', '112151111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Diaphragm - OTHER', '112151999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Cervical Cap - Initial Consultation', '112152010', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Cervical Cap - Follow up/Resupply', '112152011', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Cervical Cap - OTHER', '112152999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Method Specific Counselling', '112160119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - OTHER', '112160999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Tube/Suppositories - Initial Consultation', '112161110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Tube/Suppositories - Follow up/Resupply', '112161111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Tube/Suppositories - OTHER', '112161999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Strip - Initial Consultation', '112162110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Strip - Follow up/Resupply', '112162111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Tabs/Strip - OTHER', '112162999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Cans - Initial Consultation', '112163110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Cans - Follow up/Resupply', '112163111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Foam Cans - OTHER', '112163999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Cream & Jelly - Initial Consultation', '112164110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Cream & Jelly - Follow up/Resupply', '112164111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Cream & Jelly - OTHER', '112164999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Pessaries / C-film - Initial Consultation', '112165110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Pessaries / C-film - Follow up/Resupply', '112165111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - Spermicides - Pessaries / C-film - OTHER', '112165999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Removal', '113170112', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Method Specific Counselling', '113170119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - OTHER', '113170999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Hormone releasing IUD (5 years) - Initial Consultation/Insertion', '113171110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Hormone releasing IUD (5 years) - Follow up/Reinsertion', '113171111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Hormone releasing IUD (5 years) - OTHER', '113171999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Copper releasing IUD (10 years) - Initial Consultation/Insertion', '113172110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Copper releasing IUD (10 years) - Follow up/Reinsertion', '113172111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraceptives - IUD - Copper releasing IUD (10 years) - OTHER', '113172999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception - Voluntary Surgical Contraception (VSC) - OTHER', '120180999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Reversal', '121181112', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Method Specific Counselling', '121181119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Minilaparatomy - Follow up', '121181211', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Minilaparatomy - Contraceptive Surgery', '121181213', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Laparoscopy - Follow up', '121181311', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Laparoscopy - Contraceptive Surgery', '121181313', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Laparotomy - Follow up', '121181411', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - Laparotomy - Contraceptive Surgery', '121181413', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Female VSC - OTHER', '121181999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - Reversal', '122182112', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - Method Specific Counselling', '122182119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - Incisional vasectomy - Follow up (Sperm count)', '122182211', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - Incisional vasectomy - Contraceptive Surgery', '122182213', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - No-scalpel Vasectomy - Follow up  (Sperm count)', '122182311', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - No-scalpel Vasectomy - Contraceptive Surgery', '122182313', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception Surgical - Male VSC - OTHER', '122182999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception -  Awareness-Based Methods - OTHER', '130190999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Method Specific Counselling', '131191119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Cervical Mucous Method (CMM) - Initial Consultation/Training', '131191210', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Cervical Mucous Method (CMM) - Follow up/Training', '131191211', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Calendar Based Method (CBM) - Initial Consultation/Training', '131191310', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Calendar Based Method (CBM) - Follow up/Training', '131191311', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Sympto-thermal method - Initial Consultation/Training', '131191410', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Sympto-thermal method - Follow up/Training', '131191411', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Standard days method - Initial Consultation/Training', '131191510', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Standard days method - Follow up/Training', '131191511', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Basal Body Temperature (BBT) - Initial Consultation/Training', '131191610', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception FAB Methods - Basal Body Temperature (BBT) - Follow up/Training', '131191611', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception - FAB Methods - OTHER', '131191999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception - FP General Counselling', '141200118', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception - FP General Counselling - Combined Counselling (FP - HIV/AIDS incl. Dual protection', '141200218', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Contraception - FP General Counselling - OTHER', '141200999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Emergency Contraception Services - OTHER', '145210999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC - Counselling', '145211119', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC - Counselling - OTHER', '145211999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC - Combined Oral Contraceptives - Yuzpe - Contraceptive Supply (Treatment)', '145212110', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC - Combined Oral Contraceptives - Yuzpe - Follow Up', '145212111', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Progestogen Only Pills - Contraceptive Supply (Treatment)', '145212210', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Progestogen Only Pills - Follow Up', '145212211', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Dedicated Product - Contraceptive Supply (Treatment)', '145212310', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Dedicated Product - Follow Up', '145212311', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Copper releasing IUD - DIU Insertion (Treatment)', '145212410', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC Copper releasing IUD - Follow Up', '145212411', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'EC - Therapeutic - OTHER', '145212999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Services - OTHER', '252220999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling - Pre - Abortion Counseling', '252221129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling - Counselling on HIV Testing', '252221229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling Harm Reduction Initial Consultation', '252221329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling - OTHER', '252221999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - Exclusion of Anaemia (Haemoglobin/Hematocrit tests)', '252222121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - Tests for ABO and Rhesus (Rh) blood groups typing', '252222221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - Exclusion of ectopic pregnancy (through ultrasound)', '252222321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - Cervical cytology (Pap smear citology test)', '252222421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - HIV testing', '252222521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Diagnosis - OTHER', '252222999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Surgical) - Dilatation And Curettage (D&C)', '252223123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Surgical) - Dilatation And Evacuation (D&E) (2nd trimester of pregnancy)', '252223223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Surgical) - Manual Vacuum Aspiration (MVA)', '252223323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Surgical) - OTHER', '252223999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Medical) - Drug induced (combination of mifepristone and misopristol)', '252224122', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Induced (Medical) - Drug induced (Misoprostol Only)', '252224222', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Care - Induced (Medical) - OTHER', '252224999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion (Incomplete Abortion) - Surgical / D&C', '252225123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion (Incomplete Abortion) - Surgical / MVA', '252225223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion  (Incomplete Abortion) - Medical / Pharmaceutical', '252225722', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion (Incomplete Abortion) - Surgical/Medical OTHER', '252225999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Care - Post - Follow-up incl. Uterine Involution Monitoring & Bimanual Pelvic Exam.', '252226120', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion - Post Abortion Follow-up - OTHER (including treatment of complications)', '252226999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling - Post Abortion Counseling - Including Family Planning', '252227129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling Harm Reduction Follow-up Consultation', '252227229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Abortion Counselling - Post Abortion Counseling and family planning counselling', '252227999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Services - OTHER', '253230999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment- Anti Retro Viral (ARV)', '253231122', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment - Opportunistic Infection (OI)', '253231222', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment - Post Exposure Prophylaxis (PEP)', '253231322', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment - Psycho-Social Support', '253231422', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment - Home Care', '253231522', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Consult - Treatment - OTHER', '253231999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Antibody Lab Tests - ELISA (Blood) Test', '253232121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Antibody Lab Tests - Western Blot (WB) Assay', '253232221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Antibody Lab Tests - Indirect Immunofluorescence Assay (IFA)', '253232321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - Rapid Test (Murex-SUDS)', '253232421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - OTHER', '253232999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - Urine Test for HIV', '253233121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - Assessment of Immunologic Function (Viral Load)', '253233221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - Assessment of Immunologic Function (CD4 count)', '253233321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Other Lab Tests - OTHER', '253233999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Prevention Counselling', '253234129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Prevention Counselling - OTHER', '253234999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Counselling - PRE Test', '253235129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Counselling - POST Test (Positive) - Clients Only', '253235229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Counseling - POST Test (Negative)', '253235329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Counseling - POST Test (Positive) - Sexual Partners', '253235429', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'HIV/AIDS Counseling - OTHER', '253235999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Services - OTHER', '254240999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Counseling - Prevention Counseling', '254241129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Counseling - POST Test', '254241229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Counseling - OTHER', '254241999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Consultation - Follow Up', '254242120', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Consultation - OTHER', '254242999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Bacterial Vaginosis', '254243121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Candidiasis', '254243221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Chancroid', '254243321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Chlamydia', '254243421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Gonorrhea', '254243521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - OTHER', '254243999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Herpes Simplex (HSV)', '254244121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Human Papillomavirus (HPV)', '254244221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Syphilis', '254244321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Trichomoniasis', '254244421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Hepatitis A and B', '254244521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Test - Part I & II OTHER', '254244999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Treatment - Syndromic diagnosis with clinical treatment', '254245122', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Treatment - Etiological diagnosis with clinical treatment', '254245222', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'STI/RTI Treatment - OTHER', '254245999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Services - OTHER', '255250999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Biopsy - Conization', '255251123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Biopsy - Needle Biopsy', '255251223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Biopsy - Aspiration Biopsy', '255251323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Biopsy - Dilatation & Curretage (D&C)', '255251423', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Biopsy - OTHER', '255251999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Colposcopy', '255252123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Laparoscopy', '255252223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Hysteroscopy', '255252323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Culdoscopy', '255252423', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Hysteretomy', '255252523', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Ovariectomy', '255252623', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Mastectomy', '255252723', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - Lumpectomy', '255252823', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Endoscopy - OTHER', '255252999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - Radiography - Hysterosalpingography', '255253121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - Radiography - Mammography', '255253221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - Ultrasonography', '255253321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - Tomography', '255253421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - Dexa, Bone Density Scan', '255253521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Diagnostic Imaging - OTHER', '255253999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam - Manual Pelvic Exam (includes Palpation)', '255254121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam - Manual Breast Exam', '255254221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam - Cervical cancer screening (Pap smear)', '255254321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam - Consultation without pelvic exam', '255254421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam Cervical cancer screening Visual Inspection (VIA or VILI)'             , '255254521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam Cervical cancer screening - Liquid-based cytology (sampling procedure)', '255254621', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam Cervical cancer screening - HPV DNA test (sampling procedure)'         , '255254721', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Exam - OTHER', '255254999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Lab Test - Cytology Analysis', '255255121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Lab Test - Cytology Analysis - Liquid-based cytology', '255255221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Lab Test - Cervical cancer screening - HPV DNA test' , '255255321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Lab Test - OTHER', '255255999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Therapies - Menopause Consultations, Hormonal Replacement Therapy', '255256122', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Therapies - Menstrual regulation', '255256222', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Therapies - Female Genital Mutilation Treatment of Complications', '255256322', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Therapies - OTHER', '255256999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Surgeries - Cryosurgery - Cervical', '255257123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Surgeries - Cauterization (Cervical / Vaginal)', '255257223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Surgeries - Female Genital Mutilation Reconstructive Surgery', '255257323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Surgeries - OTHER', '255257999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - Menopause Consultations, Counseling', '255258129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - Pap Smear, Importance (pre test guidance)', '255258229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - Pap Smear, Abnormal Results (post test follow-up guidance)', '255258329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - Breast Exam Results, Mammography/Biopsy', '255258429', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - Female Genital Mutilation', '255258529', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gynecological Counselling - OTHER', '255258999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetric Services - OTHER', '256260999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Diagnosis - Fetoscopy', '256261121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Diagnosis - Ultrasonography, Pre-natal', '256261221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Diagnosis - Pelvimetry', '256261321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Diagnosis - Placental Function Tests', '256261421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Diagnosis - OTHER', '256261999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Care - Uterine Monitoring', '256262121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Care - Fetal Monitoring', '256262221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Care - Immunisations', '256262422', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Care - OTHER', '256262999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Counselling - Pre Natal Care Info', '256263129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Counselling - Unplanned Pregnancy', '256263229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Counselling - HIV Prevention and Testing', '256263329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre natal Counselling - OTHER', '256263999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Lab Tests - Pregnancy Tests - Agglutination Inhibition - Urine 1 test', '256264121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Lab Tests - Pregnancy Tests - Radioimmunoasays - Blood test', '256264221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Lab Tests - Pregnancy Tests - OTHER', '256264999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Urine 1', '256265121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Glicemia de Jejum', '256265221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Hemoglobin (HB)', '256265321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Blood Type', '256265421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - VDRL', '256265521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - HIV', '256265621', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Amniocentesis', '256265721', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - Chorionic Villi Sampling', '256265821', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Pre-Natal Lab Tests - OTHER', '256265999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Surgery - Child Birth, Vaginal Delivery', '256267123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Surgery - Child Birth, Cesarean Delivery', '256267223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Surgery - Emergency Obstetric Care (EmOC)', '256267323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Surgery - OTHER', '256267999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post natal Care - Consultation including Uterine Involution Monitoring', '256268120', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post natal Care - OTHER', '256268999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post-Natal Counselling - FP Methods', '256269129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post-Natal Counselling - Breastfeeding Advice', '256269229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post-Natal Counselling - HIV Counselling', '256269329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Obstetrics - Post-Natal Counselling - OTHER', '256269999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Services - OTHER', '257270999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Endoscopy - Cystoscopy', '257271123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Endoscopy - Ureteroscopy', '257271223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Endoscopy - OTHER', '257271999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnostic Imaging - Urography', '257272121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnostic Imaging - OTHER', '257272999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnosis Other - Exam', '257273121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnosis Other - Prostate Cancer Screening', '257273221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnosis Other - Peniscopy', '257273321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnosis Other - Other Urogenital Services', '257273421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Diagnosis Other - OTHER', '257273999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Male Surgery - Biopsy', '257274123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Male Surgery - Circumcision', '257274223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Male Surgery - Other Surgical Services', '257274323', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Urological Male Surgery - OTHER', '257274999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility/Subfertility - OTHER', '258280999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Biopsy - Endometrial biopsy', '258281123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Biopsy - Testicular biopsy', '258281223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Biopsy - OTHER', '258281999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Endoscopy - Laparoscopy', '258282123', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Endoscopy - Histeroscopy', '258282223', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Endoscopy - OTHER', '258282999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Diagnostic Imaging - Histerosalpingography', '258283121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Diagnostic Imaging - Ovarian ultrasound', '258283221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Diagnostic Imaging - Transvaginal ecography', '258283321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Diagnostic Imaging - OTHER', '258283999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Post-coital test or Sims-Huhner test', '258284121', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Fallopian Tube Patency Tests', '258284221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Clomiphene citrate challenge test (CCCT)', '258284321', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Semen analysis', '258284421', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Basal Temperature', '258284521', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Mucose Analysis', '258284621', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Sperm Count', '258284721', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Spermiogram', '258284821', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - Hormonal analysis', '258284921', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Lab Test - OTHER', '258284999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Ovulation Induction', '258286122', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Embryo Transfer', '258286222', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Fertilization in Vitro', '258286322', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Gamete Intrafallopian Transfer', '258286422', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Artificial Insemination', '258286522', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Oocyte Donation', '258286622', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - Zygote Intrafallopian Transfer', '258286722', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility Treatment - OTHER', '258286999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility/Subfertility Consultation', '258288120', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility/Subfertility Consultation - OTHER', '258288999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility/Subfertility  Counseling', '258289129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Infertility/Subfertility  Counseling - OTHER', '258289999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other Specialized Counselling Services - OTHER', '260290999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Individual Counseling', '261291129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Support Groups for Survivors', '261291229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Legal Counseling', '261291329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Intimate Partner Sexual Abuse', '261291429', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Intimate Partner Physical  Abuse', '261291529', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Intimate Partner Emotional Abuse', '261291629', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - NonIntimate Partner Sexual Assalt/Rape', '261291729', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - Screening Only  - Gender Based Violence (GBV)', '261291829', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - GBV - OTHER', '261291999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Domestic Violence, Child Abuse', '262292129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Domestic Violence, Screening  Only Child Abuse', '262292229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Domestic Violence - OTHER', '262292999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Family - Parent/Child Relationship', '262293129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Family- Family Conflict', '262293229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Family, Delinquency', '262293329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Family - OTHER', '262293999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Pre-Marital including Pre-Marital Family Planning', '262294129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Marital - Relationship, Partner Negotiation', '262294229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Marital - Sexuality / Sexual Disfunction', '262294329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Marital - OTHER', '262294999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Youth - Life Skills Counseling', '262295129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Youth - Sexuality', '262295229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Youth - Telephone / Internet Hotline Counseling', '262295329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Youth - SRH Counselling', '262295429', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Youth - OTHER', '262295999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Male - SRH Counselling', '262296129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Male - Sexuality', '262296229', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Male - GBV', '262296329', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counselling - Male - OTHER', '262296999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counseling - Other - Sexuality Issues ( 25 years and over)', '263297129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Counseling - OTHER', '263297999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other SRH medical services - Consultation'       , '269298120', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other SRH medical services - Diagnostic Test'    , '269298221', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other SRH medical services - Therapy / Treatment', '269298322', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other SRH medical services - Surgery'            , '269298423', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other SRH medical services - OTHER'              , '269298999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Medical Specialties - System Oriented Services - OTHER', '371300999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Angiology - Consultation', '371301130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Angiology - Diagnostic Test', '371301231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Angiology - Therapy / Treatment', '371301332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Angiology - Surgery', '371301433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Angiology - OTHER', '371301999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Cardiology - Consultation', '371311130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Cardiology - Diagnostic EKG', '371311231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Cardiology - Therapy / Treatment', '371311332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Cardiology - Surgery', '371311433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Cardiology - OTHER', '371311999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry - Diagnosis', '371321131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry -Therapy / Treatment', '371321232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry - Orthodontics', '371321332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry - Periodontics', '371321432', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry - Surgery', '371321533', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dentistry - OTHER', '371321999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dermatology - Consultation', '371331130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dermatology - Diagnostic Test', '371331231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dermatology - Therapy / Treatment', '371331332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dermatology - Surgery', '371331433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Dermatology - OTHER', '371331999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Endocrinology - Consultation', '371341130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Endocrinology - Diagnostic Test', '371341231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Endocrinology - Therapy / Treatment', '371341332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Endocrinology - Surgery', '371341433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Endocrinology - OTHER', '371341999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gastroenterology - Consultation', '371351130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gastroenterology - Diagnostic Test', '371351231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gastroenterology - Therapy / Treatment', '371351332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gastroenterology - Surgery', '371351433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Gastroenterology - OTHER', '371351999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Genetics - Counselling', '371361129', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Genetics - Consultation', '371361230', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Genetics - Diagnostic Test', '371361331', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Genetics - Therapy / Treatment', '371361432', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Genetics - OTHER', '371361999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Nephrology - Consultation', '371371130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Nephrology - Diagnostic Test', '371371231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Nephrology - Therapy / Treatment', '371371332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Nephrology - Surgery', '371371433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Nephrology - OTHER', '371371999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neumology - Consultation', '371381130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neumology - Diagnostic Test', '371381231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neumology - Therapy / Treatment', '371381332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neumology - Surgery', '371381433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neumology - OTHER', '371381999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neurology - Consultation', '371391130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neurology - Diagnostic Exam', '371391231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neurology - Therapy / Treatment', '371391332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neurology - Surgery', '371391433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Neurology - OTHER', '371391999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Ophtalmology - Consultation', '371401130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Ophtalmology - Diagnostic Exam', '371401231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Ophtalmology - Therapy / Treatment', '371401332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Ophtalmology - Surgery', '371401433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Ophtalmology - OTHER', '371401999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Orthopedics - Consultation', '371411130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Orthopedics - Diagnostic Exam', '371411231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Orthopedics - Therapy / Treatment', '371411332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Orthopedics - Surgery', '371411433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Orthopedics - OTHER', '371411999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Othorhinolaringology - Consultation', '371421130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Othorhinolaringology - Diagnostic Exam', '371421231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Othorhinolaringology - Therapy / Treatment', '371421332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Othorhinolaringology - Surgery', '371421433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Othorhinolaringology - OTHER', '371421999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Podology - Consultation', '371431130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Podology - Diagnostic Exam', '371431231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Podology - Therapy / Treatment', '371431332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Podology - Surgery', '371431433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Podology - OTHER', '371431999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rheumatology - Consultation', '371441130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rheumatology - Diagnostic Exam', '371441231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rheumatology - Therapy / Treatment', '371441332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rheumatology - Surgery', '371441433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rheumatology - OTHER', '371441999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Medical Specialties - Disease Oriented Services - OTHER', '372500999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Optometry - Consultation', '372501130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Optometry - Diagnostic Exam', '372501231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Optometry - OTHER', '372501999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychiatry - Diagnostic consultation', '372511131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychiatry - Therapy / Treatment', '372511232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychiatry - OTHER', '372511999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychology - Diagnostic consultation', '372521131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychology - Therapy / Treatment', '372521232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Psychology - OTHER', '372521999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Radiology - Diagnostic Imaging', '372531131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Radiology - Therapy / Treatment', '372531232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Radiology - OTHER', '372531999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Oncology - Diagnostic Test', '372541131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Oncology - Therapy / Treatment', '372541232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Oncology - Surgery', '372541333', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Oncology - OTHER', '372541999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Allergy - Consultation', '372551130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Allergy - Diagnostic Test', '372551231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Allergy - Therapy / Treatment', '372551332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Allergy - OTHER', '372551999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Immunology - Consultation', '372561130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Immunology - Diagnostic Test', '372561231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Immunology - OTHER', '372561999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Medical Specialties - Community Oriented Services - OTHER', '373600999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Hypertension Screening', '373601131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Physical Exam', '373601231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Weight & Vital Signs', '373601331', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Diabetes Screening', '373601431', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Urinalysis', '373601531', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Cholesterol screening', '373601631', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Nutrition Counseling', '373601729', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health -  Diet/Weight Control Counseling', '373601829', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Family Health - OTHER', '373601999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Geriatrics - Consultation', '373621130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Geriatrics - Diagnostic Test', '373621231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Geriatrics - Therapy / Treatment', '373621332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Geriatrics - OTHER', '373621999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Consultation', '373641130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Diagnostic - Neonatal Screening (at Birth)', '373641231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Diagnostic - Well Baby Care / Infant Health Check', '373641331', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Therapy / Treatment - Nutrition', '373641432', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Therapy / Treatment - Immunization', '373641532', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Therapy / Treatment - Oral rehydration (ORT/ORS)', '373641632', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Therapy / Treatment - Neonatal Intensive Care', '373641732', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - Surgery - Circumcision', '373641833', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pediatrics - OTHER', '373641999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Physical Medicine & Rehabilitation - Consultation', '373661130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Physical Medicine & Rehabilitation - Diagnostic Test', '373661231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Physical Medicine & Rehabilitation - Therapy / Treatment', '373661332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Physical Medicine & Rehabilitation - Surgery', '373661433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Physical Medicine & Rehabilitation - OTHER', '373661999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Preventive Medicine - Consultation', '373671130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Preventive Medicine - Diagnostic Test', '373671231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Preventive Medicine - OTHER', '373671999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Emergency Medicine - Evaluation', '373681131', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Emergency Medicine - Initial Treatment', '373681232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Emergency Medicine - Emergency Surgery', '373681333', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Emergency Medicine - OTHER', '373681999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hospitalization - Ambulatory (1 day)', '373691140', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hospitalization - Extended (>1day)', '373691241', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hospitalization - OTHER', '373691999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Medical Specialties - Diagnostic/Therapeutic Procedures - OTHER', '374700999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hematology - Consultation', '374701130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hematology - Diagnostic Test', '374701231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hematology - Therapy / Treatment', '374701332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Hematology - OTHER', '374701999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Toxicology - Consultation', '374721130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Toxicology - Diagnostic tests', '374721231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Toxicology - Therapy / Treatment', '374721332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Toxicology - OTHER', '374721999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chemical Patology - Consultation', '374741130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chemical Patology - Diagnostic Test', '374741231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chemical Patology - OTHER', '374751999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pathology - Consultation', '374761130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pathology - Diagnostic Test', '374761231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Pathology - OTHER', '374761999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Microbiology - Consultation', '374781130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Microbiology - Diagnostic Test', '374781231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Microbiology - OTHER', '374781999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Medical Specialties - Other Services - OTHER', '375800999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chiropractice - Consultation', '375801130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chiropractice - Therapy / Treatment', '375801232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Chiropractice - OTHER', '375801999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Osteophaty - Consultation', '375811130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Osteophaty - Therapy / Treatment', '375811232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Osteophaty - Diagnostic Test', '375811331', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Osteophaty - OTHER', '375811999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Plastic Surgery - Consultation', '375821130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Plastic Surgery - Therapy / Treatment', '375821232', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Plastic Surgery - Surgery', '375821333', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Plastic Surgery - OTHER', '375821999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - Consultation'       , '375831130', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - Diagnostic Test'    , '375831231', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - Therapy / Treatment', '375831332', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - Surgery'            , '375831433', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - Counselling'        , '375831539', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-SRH medical services - OTHER'              , '375831999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'ALL OTHER NON SRH SERVICES - OTHER', '380910999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Sales & Rentals - OTHER', '380911999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Sales of Medicines', '381912150', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Sales Medical Supplies', '381912250', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Sales Medical Equipment', '381912350', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Sales - OTHER', '381913999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rental Medical Infrastructure', '382914450', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rental Medical Infrastructure', '382914450', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Rental Medical Equipment / Infrastructure - OTHER', '382915999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-medical Products & Services - Sales of IEC Materials'            , '491990190', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-medical Products & Services - Free distribution of IEC materials', '491990290', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other non-medical Products & Services - Other Generic Products'            , '491990999', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other Non Medical Products & Services - Sales of IEC Services', '492992090', 11, '' );
INSERT INTO codes ( code_text, code, code_type, modifier ) VALUES ( 'Other Non Medical Products & Services - OTHER', '492992999', 11, '' );
#EndIf

# Following lines mirror line-for-line the spreadsheet "CYP Factors 2010.1.xlsx".
UPDATE codes SET cyp_factor = 0.0666667 WHERE code_type = 11 AND code LIKE '11110_%';
UPDATE codes SET cyp_factor = 0.0769230 WHERE code_type = 11 AND code LIKE '111111%';
UPDATE codes SET cyp_factor = 0.1666667 WHERE code_type = 11 AND code LIKE '111112%';
UPDATE codes SET cyp_factor = 0.2500000 WHERE code_type = 11 AND code LIKE '111113%';
UPDATE codes SET cyp_factor = 3.5000000 WHERE code_type = 11 AND code LIKE '111122%';
UPDATE codes SET cyp_factor = 3.5000000 WHERE code_type = 11 AND code LIKE '111123%';
UPDATE codes SET cyp_factor = 2.5000000 WHERE code_type = 11 AND code LIKE '111124%';
UPDATE codes SET cyp_factor = 0.0666667 WHERE code_type = 11 AND code LIKE '111132%';
UPDATE codes SET cyp_factor = 0.0666667 WHERE code_type = 11 AND code LIKE '111133%';
UPDATE codes SET cyp_factor = 0.0083333 WHERE code_type = 11 AND code LIKE '112141%';
UPDATE codes SET cyp_factor = 0.0083333 WHERE code_type = 11 AND code LIKE '112142%';
UPDATE codes SET cyp_factor = 1.0000000 WHERE code_type = 11 AND code LIKE '112151%';
UPDATE codes SET cyp_factor = 1.0000000 WHERE code_type = 11 AND code LIKE '112152%';
UPDATE codes SET cyp_factor = 0.1333333 WHERE code_type = 11 AND code LIKE '112161%';
UPDATE codes SET cyp_factor = 0.1333333 WHERE code_type = 11 AND code LIKE '112162%';
UPDATE codes SET cyp_factor = 0.1333333 WHERE code_type = 11 AND code LIKE '112163%';
UPDATE codes SET cyp_factor = 0.1333333 WHERE code_type = 11 AND code LIKE '112164%';
UPDATE codes SET cyp_factor = 0.1333333 WHERE code_type = 11 AND code LIKE '112165%';
UPDATE codes SET cyp_factor = 3.5000000 WHERE code_type = 11 AND code LIKE '113171%';
UPDATE codes SET cyp_factor = 3.5000000 WHERE code_type = 11 AND code LIKE '113172%';
UPDATE codes SET cyp_factor = 10.000000 WHERE code_type = 11 AND code LIKE '121181%';
UPDATE codes SET cyp_factor = 10.000000 WHERE code_type = 11 AND code LIKE '122182%';
UPDATE codes SET cyp_factor = 0.0500000 WHERE code_type = 11 AND code LIKE '145212%';
# Next line clears cyp for codes corresponding to removal of contraception.
UPDATE codes SET cyp_factor = 0         WHERE code_type = 11 AND code LIKE '1_____112';

#IfMissingColumn patient_data contrastart
ALTER TABLE patient_data ADD contrastart DATE DEFAULT NULL;
#EndIf

#IfMissingColumn patient_data ippfconmeth
ALTER TABLE patient_data ADD ippfconmeth varchar(255) NOT NULL DEFAULT '';
#EndIf

#IfNotRow2D list_options list_id lbfnames option_id LBFcontra
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lbfnames','LBFcontra','Contraception',1);
DELETE FROM layout_options WHERE form_id = 'LBFcontra';
INSERT INTO layout_options VALUES ('LBFcontra','contratype' ,'1','Action'    , 1,1,2, 0, 0,'contratype' ,1,3,'','' ,'Contraception action');
INSERT INTO layout_options VALUES ('LBFcontra','ippfconmeth','1','Method'    , 2,1,1, 0, 0,'ippfconmeth',1,3,'','' ,'Contraception method');
INSERT INTO layout_options VALUES ('LBFcontra','contrastart','1','Start Date', 3,4,1,10,10,''           ,1,3,'','D','Contraception start date');
# This section creates LBFcontra visit forms to replace contraception dates/methods in demographics.
INSERT INTO lbf_data (field_id, field_value)
  SELECT 'contratype', pd.pid FROM patient_data AS pd, form_encounter AS fe
  WHERE pd.contrastart IS NOT NULL
  AND fe.pid = pd.pid
  AND fe.date >= pd.contrastart
  GROUP BY pd.pid;
INSERT INTO lbf_data (form_id, field_id, field_value)
  SELECT MAX(ld.form_id), 'contrastart', pd.contrastart
  FROM patient_data AS pd, form_encounter AS fe, lbf_data AS ld
  WHERE pd.contrastart IS NOT NULL
  AND fe.pid = pd.pid
  AND fe.date >= pd.contrastart
  AND ld.field_id = 'contratype'
  AND ld.field_value = pd.pid
  GROUP BY pd.pid;
INSERT INTO lbf_data (form_id, field_id, field_value)
  SELECT MAX(ld.form_id), 'ippfconmeth', pd.ippfconmeth
  FROM patient_data AS pd, form_encounter AS fe, lbf_data AS ld
  WHERE pd.contrastart IS NOT NULL
  AND pd.ippfconmeth != ''
  AND fe.pid = pd.pid
  AND fe.date >= pd.contrastart
  AND ld.field_id = 'contratype'
  AND ld.field_value = pd.pid
  GROUP BY pd.pid;
INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, formdir)
  SELECT CURRENT_DATE, MIN(fe.encounter), 'Contraception Start', MAX(ld.form_id), pd.pid, 'admin', 'Default', '1', 'LBFcontra'
  FROM patient_data AS pd, form_encounter AS fe, lbf_data AS ld
  WHERE pd.contrastart IS NOT NULL
  AND fe.pid = pd.pid
  AND fe.date >= pd.contrastart
  AND ld.field_id = 'contratype'
  AND ld.field_value = pd.pid
  GROUP BY pd.pid;
UPDATE forms AS f, lbf_data AS ld SET ld.field_value = '3' WHERE
  f.formdir = 'LBFcontra' AND f.deleted = 0 AND ld.form_id = f.form_id AND
  ld.field_id = 'contratype' AND ld.field_value = f.pid;
#EndIf

ALTER TABLE patient_data DROP ippfconmeth;
ALTER TABLE patient_data DROP contrastart;
DELETE FROM `layout_options` WHERE form_id = 'DEM' AND field_id = 'ippfconmeth';
DELETE FROM `layout_options` WHERE form_id = 'DEM' AND field_id = 'contrastart';

