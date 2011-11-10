DELETE FROM layout_options WHERE form_id = 'HIS';

INSERT INTO layout_options VALUES ('HIS','usertext12'          ,'1Personal','Blood Group'         , 1, 1,1, 0,  0,'bloodgroup' ,1,1,'','' ,'Blood Group');
INSERT INTO layout_options VALUES ('HIS','usertext13'          ,'1Personal','RH Factor'           , 2, 1,1, 0,  0,'rh_factor'  ,1,1,'','' ,'RH Factor');
INSERT INTO layout_options VALUES ('HIS','usertext11'          ,'1Personal','Risk Factors'        , 3,21,1, 0,  0,'riskfactors',1,1,'','' ,'Risk Factors');
INSERT INTO layout_options VALUES ('HIS','exams'               ,'1Personal','Exams/Tests'         , 4,23,1, 0,  0,'exams'      ,1,1,'','' ,'Exam and test results');
INSERT INTO layout_options VALUES ('HIS','usertext14'          ,'1Personal','Surgical History'    , 5,25,1, 0,  0,'surghist'   ,1,3,'','' ,'Surgeries with dates/notes');
INSERT INTO layout_options VALUES ('HIS','coffee'              ,'1Personal','Coffee'              , 6, 2,1,20,255,''           ,1,1,'','' ,'Caffeine consumption');
INSERT INTO layout_options VALUES ('HIS','tobacco'             ,'1Personal','Tobacco'             , 7, 2,1,20,255,''           ,1,1,'','' ,'Tobacco use');
INSERT INTO layout_options VALUES ('HIS','alcohol'             ,'1Personal','Alcohol'             , 8, 2,1,20,255,''           ,1,1,'','' ,'Alcohol consumption');
INSERT INTO layout_options VALUES ('HIS','sleep_patterns'      ,'1Personal','Sleep Patterns'      , 9, 2,1,20,255,''           ,1,1,'','' ,'Sleep patterns');
INSERT INTO layout_options VALUES ('HIS','exercise_patterns'   ,'1Personal','Exercise Patterns'   ,10, 2,1,20,255,''           ,1,1,'','' ,'Exercise patterns');
INSERT INTO layout_options VALUES ('HIS','seatbelt_use'        ,'1Personal','Seatbelt Use'        ,11, 2,1,20,255,''           ,1,1,'','' ,'Seatbelt use');
INSERT INTO layout_options VALUES ('HIS','counseling'          ,'1Personal','Counseling'          ,12, 2,1,20,255,''           ,1,1,'','' ,'Counseling activities');
INSERT INTO layout_options VALUES ('HIS','hazardous_activities','1Personal','Hazardous Activities',13, 2,1,20,255,''           ,1,1,'','' ,'Hazardous activities');

INSERT INTO layout_options VALUES ('HIS','history_father'               ,'2Relatives','Father'             , 1, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','history_mother'               ,'2Relatives','Mother'             , 2, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','history_siblings'             ,'2Relatives','Siblings'           , 3, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','history_spouse'               ,'2Relatives','Spouse'             , 4, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','history_offspring'            ,'2Relatives','Offspring'          , 5, 2,1,20,255,'',1,3,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_cancer'             ,'2Relatives','Cancer'             , 6, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_tuberculosis'       ,'2Relatives','Tuberculosis'       , 7, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_diabetes'           ,'2Relatives','Diabetes'           , 8, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_high_blood_pressure','2Relatives','High Blood Pressure', 9, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_heart_problems'     ,'2Relatives','Heart Problems'     ,10, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_stroke'             ,'2Relatives','Stroke'             ,11, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_epilepsy'           ,'2Relatives','Epilepsy'           ,12, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_mental_illness'     ,'2Relatives','Mental Illness'     ,13, 2,1,20,255,'',1,1,'','' ,'');
INSERT INTO layout_options VALUES ('HIS','relatives_suicide'            ,'2Relatives','Suicide'            ,14, 2,1,20,255,'',1,3,'','' ,'');

INSERT INTO layout_options VALUES ('HIS','usertext15','3Reproductive Women','Menstrual', 1,22,1, 0,  0,'genmenhist',1,1,'','' ,'Menstrual History');
INSERT INTO layout_options VALUES ('HIS','usertext16','3Reproductive Women','Obstetric', 2,22,1, 0,  0,'genobshist',1,1,'','' ,'Obstetric History');
INSERT INTO layout_options VALUES ('HIS','usertext17','3Reproductive Women','Abortion' , 3,22,1, 0,  0,'genabohist',1,1,'','' ,'Abortion History');

INSERT INTO layout_options VALUES ('HIS','usertext18','4Reproductive General','HIV/AIDS' , 1,21,1, 0,  0,'genhivhist',1,1,'','' ,'HIV/AIDS History');
INSERT INTO layout_options VALUES ('HIS','usertext19','4Reproductive General','ITS/ITR'  , 2,21,1, 0,  0,'genitshist',1,1,'','' ,'ITS/ITR History');
INSERT INTO layout_options VALUES ('HIS','usertext20','4Reproductive General','Fertility', 3,21,1, 0,  0,'genferhist',1,1,'','' ,'Infertility/Subfertility History');
INSERT INTO layout_options VALUES ('HIS','usertext21','4Reproductive General','Urology'  , 4,21,1, 0,  0,'genurohist',1,1,'','' ,'Urology History');

INSERT INTO layout_options VALUES ('HIS','name_1'            ,'5Other','Name/Value'        ,1, 2,1,10,255,'',1,1,'','' ,'Name 1' );
INSERT INTO layout_options VALUES ('HIS','value_1'           ,'5Other',''                  ,2, 2,1,10,255,'',0,0,'','' ,'Value 1');
INSERT INTO layout_options VALUES ('HIS','name_2'            ,'5Other','Name/Value'        ,3, 2,1,10,255,'',1,1,'','' ,'Name 2' );
INSERT INTO layout_options VALUES ('HIS','value_2'           ,'5Other',''                  ,4, 2,1,10,255,'',0,0,'','' ,'Value 2');
INSERT INTO layout_options VALUES ('HIS','additional_history','5Other','Additional History',5, 3,1,30,  3,'',1,3,'' ,'' ,'Additional history notes');
INSERT INTO layout_options VALUES ('HIS','userarea11','5Other','User Defined Area 11',6,3,0,30,3,'',1,3,'','','User Defined');
INSERT INTO layout_options VALUES ('HIS','userarea12','5Other','User Defined Area 12',7,3,0,30,3,'',1,3,'','','User Defined');

DELETE FROM list_options WHERE list_id = 'surghist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','gi'    ,'Gastrointestinal Tract',  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','bil'   ,'Biliary System'        ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','spleen','Spleen'                ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','liver' ,'Liver'                 ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','pan'   ,'Pancreas'              ,  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','hernia','Hernia'                ,  6,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','endo'  ,'Endocrine Glands'      ,  7,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','thorax','Thorax'                ,  8,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('surghist','oth'   ,'Others'                ,  9,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','surghist','Surgical History',110,0,0);

DELETE FROM list_options WHERE list_id = 'genmenhist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','agemen'   ,'Age of Menarche'                     ,  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','durabs'   ,'Duration of Absence (days)'          ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','days'     ,'Average No. of Days'                 ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','lmp'      ,'Last Normal Period (date)'           ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','cyclen'   ,'Cycle Length (days)'                 ,  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','flowlen'  ,'Flow Length (days)'                  ,  6,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','bleedflow','Flow Bleeding (L/M/H)'               ,  7,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genmenhist','bleedslp' ,'Bleeding/spotting since last period?',  8,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genmenhist','General Menstrual History',111,0,0);

DELETE FROM list_options WHERE list_id = 'genobshist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','npreg','Total No. of Pregnancies'        ,  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','agefp','Age at First Pregnancy'          ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','nlc'  ,'Number of Living Children'       ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','nacw' ,'How Many More Children Wanted'   ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','nclw' ,'No. Children Now Living with You',  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','nlb'  ,'Number of Live Births'           ,  6,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','nvd'  ,'Number of Vaginal Deliveries'    ,  7,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','ncs'  ,'Number of C Sections'            ,  8,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genobshist','dlpe' ,'Date Last Pregnancy Ended'       ,  9,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genobshist','General Obstetric History',112,0,0);

DELETE FROM list_options WHERE list_id = 'genabohist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genabohist','age1st','Age at First Abortion'                 ,  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genabohist','nmis'  ,'No. Miscarriages/Spontaneous Abortions',  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genabohist','nstill','Number of Stillbirths'                 ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genabohist','ntubal','No. of Tubal Pregnancies (Ectopic)'    ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genabohist','nia'   ,'Number of Induced Abortions'           ,  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genabohist','General Abortion History',113,0,0);

DELETE FROM list_options WHERE list_id = 'genhivhist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','info'  ,'Client is informed about HIV transmission'       ,  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','couns' ,'Client has received HIV Test Counselling'        ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','test'  ,'Client has been HIV tested'                      ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','hpos'  ,'Client has tested HIV positive'                  ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','apos'  ,'Client has developed AIDS'                       ,  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','infect','Client has developed an oportunistic infection'  ,  6,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','treat' ,'Client has received HIV/AIDS treatment'          ,  7,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','psych' ,'Client has received psycho-social support'       ,  8,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genhivhist','discr' ,'Client has  been victim of stigma/discrimination',  9,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genhivhist','HIV/AIDS Basic History',114,0,0);

DELETE FROM list_options WHERE list_id = 'genitshist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genitshist','info' ,'Client is informed about ITS transmission',  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genitshist','couns','Client has received ITS  Counselling'     ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genitshist','test' ,'Client has been ITS tested'               ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genitshist','pos'  ,'Client has tested ITS positive'           ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genitshist','treat','Client has received ITS treatment'        ,  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genitshist','ITS/ITR Basic History',115,0,0);

DELETE FROM list_options WHERE list_id = 'genferhist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genferhist','nc12','No conception after 12 months w/o FP & female <34 years',  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genferhist','nc6' ,'No conception after 6 months w/o FP & female >35 years' ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genferhist','finc','Female is incapable of carrying a pregnancy to term'    ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genferhist','year','Tried unsuccessfully to have a child for a year or more',  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genferhist','Infertility/Subfertility Basic History',116,0,0);

DELETE FROM list_options WHERE list_id = 'genurohist';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','ucon'  ,'Client reports urinary concerns'       ,  1,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','icon'  ,'Client reports incontinency concerns'  ,  2,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','infect','Client reports genital infection'      ,  3,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','pain'  ,'Client reports genital pain'           ,  4,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','treat' ,'Client has undergone genital treatment',  5,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('genurohist','sur'   ,'Client has undergone genital surgery'  ,  6,0,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','genurohist','Urology Basic History',117,0,0);

DELETE FROM list_options WHERE list_id = 'occupations' AND option_id = 'oth';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('occupations','oth','Other', 1,0,0);
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'occupations';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, option_value ) VALUES ('lists','occupations','Occupations',61,0,0);
UPDATE layout_options SET data_type = 26, list_id = 'occupations'  WHERE form_id = 'DEM' AND field_id = 'occupation';
UPDATE layout_options SET data_type = 26, title = 'Religion'       WHERE form_id = 'DEM' AND field_id = 'userlist5';
UPDATE layout_options SET data_type = 26, title = 'Monthly Income' WHERE form_id = 'DEM' AND field_id = 'userlist3';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'ethnoracial';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'language';
UPDATE layout_options SET data_type = 26 WHERE form_id = 'DEM' AND field_id = 'status';
UPDATE layout_options SET uor = 0 WHERE form_id = 'DEM' AND field_id = 'providerID';

UPDATE layout_options AS a, list_options AS i SET a.group_name = '1Who', a.title = 'Transgender', a.seq = 13, a.data_type = 26, a.uor = 1, a.description = 'Transgender', i.title = 'Transgender' WHERE a.form_id = 'DEM' AND a.field_id = 'userlist6' AND a.uor = 0 AND i.list_id = 'lists' AND i.option_id = 'userlist6';

UPDATE list_options SET title = 'Education' WHERE list_id = 'lists' AND option_id = 'userlist2';
DELETE FROM list_options WHERE list_id = 'userlist2';
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','1','Illiterate',1,0,'0');
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','2','Basic Schooling',2,1,'1');
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, mapping ) VALUES ('userlist2','3','Advanced Schooling',3,0,'2');

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
INSERT INTO `openemr_postcalendar_categories` VALUES (12,'3 Counselling Only','#FFFFCC','Counselling',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);
INSERT INTO `openemr_postcalendar_categories` VALUES (13,'4 Supply/Re-Supply','#CCCCCC','Supply/Re-Supply',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);
INSERT INTO `openemr_postcalendar_categories` VALUES (14,'5 Administrative','#FFFFFF','Supply/Re-Supply',1,NULL,'a:5:{s:17:\"event_repeat_freq\";s:1:\"1\";s:22:\"event_repeat_freq_type\";s:1:\"4\";s:19:\"event_repeat_on_num\";s:1:\"1\";s:19:\"event_repeat_on_day\";s:1:\"0\";s:20:\"event_repeat_on_freq\";s:1:\"0\";}',0,900,0,3,2,0,0);

INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'full_new_patient_form'       , 0, '3' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'patient_search_results_style', 0, '1' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'patient_search_results_sort' , 0, '1' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'gbl_patient_initial_bottom'  , 0, '1' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'gbl_encounters_default_view' , 0, '1' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'gbl_rapid_workflow'          , 0, 'LBFmsivd' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'simplified_demographics'     , 0, '1' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'online_support_link'         , 0, ''  );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'units_of_measurement'        , 0, '2' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'specific_application'        , 0, '2' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'inhouse_pharmacy'            , 0, '2' );
INSERT INTO globals ( gl_name, gl_index, gl_value ) VALUES ( 'configuration_import_export' , 0, '1' );

DELETE FROM code_types;
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('MA'  ,12, 1, 0, '', 1, 1, 0, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('IPPF',11, 2, 0, '', 0, 0, 1, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('ICD9', 2, 3, 2, '', 0, 0, 0, 1);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('ACCT',13, 4, 0, '', 0, 0, 1, 0);
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag ) VALUES ('REF' ,16, 5, 0, '', 0, 1, 1, 0);

DELETE FROM layout_options WHERE form_id = 'REF';
INSERT INTO layout_options VALUES ('REF','refer_date'        ,'1Referral','Referral Date'                  , 5, 4,2, 0,  0,''         ,1,1,'C','D','Date of referral');
INSERT INTO layout_options VALUES ('REF','refer_from'        ,'1Referral','Referred By'                    ,10,10,2, 0,  0,''         ,1,1,'' ,'' ,'Referral By');
INSERT INTO layout_options VALUES ('REF','refer_external'    ,'1Referral','External Referral'              ,15, 1,2, 0,  0,'boolean'  ,1,1,'' ,'' ,'External referral?');
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

INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('lists','actorest','Actual or Estimated', 1,0);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('actorest','act'  ,'Actual'   ,10,1);
INSERT INTO list_options ( list_id, option_id, title, seq, is_default ) VALUES ('actorest','est'  ,'Estimated',20,0);
UPDATE layout_options SET group_name = '1Who', title='', seq = 7, data_type = 1,
  uor = 1, fld_length = 0, list_id = 'actorest', titlecols = 0, datacols = 0,
  description = 'Indicates if DOB is estimated' WHERE
  form_id = 'DEM' AND field_id = 'usertext3' AND uor = 0;

UPDATE layout_options SET data_type = 14 WHERE form_id = 'REF' AND field_id = 'refer_from';
UPDATE layout_options SET title='Referral Type', list_id = 'reftype'  WHERE form_id = 'REF' AND field_id = 'refer_external' AND list_id = 'boolean';

insert into lang_definitions ( cons_id, lang_id, definition ) select lc.cons_id, 1, 'SDP ID'   from lang_constants as lc left join lang_definitions as ld on ld.cons_id = lc.cons_id and ld.lang_id = 1 where lc.constant_name = 'CLIA Number' and ld.cons_id is null;

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

INSERT INTO lang_constants ( constant_name ) VALUES ( 'POS Code' );
INSERT INTO lang_definitions ( cons_id, lang_id, definition ) SELECT lc.cons_id, 1, 'COD Code' FROM lang_constants AS lc LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND ld.lang_id = 1 WHERE lc.constant_name = 'POS Code' AND ld.cons_id IS NULL;

CREATE INDEX `encounter` ON `forms` (`encounter`);
CREATE INDEX `encounter` ON `drug_sales` (`encounter`);
CREATE INDEX `encounter` ON `billing` (`encounter`);

