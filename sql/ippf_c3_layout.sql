DELETE FROM layout_options WHERE form_id = 'LBFgcac' AND group_name LIKE '_IPPA CAC Section';
--                                 form_id   field_id        group               title                          seq T UOR FL ML list_id       TC DC DV EO Desc
INSERT INTO layout_options VALUES ('LBFgcac','gc_howarrived','3IPPA CAC Section','How Arrived at the Clinic'    ,15, 1,1, 0, 0,'gc_howarrived',1,3,'','', 'Reason for rejecting or referring services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_rtypeserv' ,'3IPPA CAC Section','Services Requested'           ,16, 1,1, 0, 0,'gc_typeserv'  ,1,3,'','', 'Type of services requested');
INSERT INTO layout_options VALUES ('LBFgcac','gc_ptypeserv' ,'3IPPA CAC Section','Services Provided'            ,17, 1,0, 0, 0,'gc_typeserv'  ,1,3,'','', 'Type of services actually provided');
INSERT INTO layout_options VALUES ('LBFgcac','gc_menreg'    ,'3IPPA CAC Section','For Menstrual Regulation'     ,18, 1,1, 0, 0,'gc_menreg'    ,1,3,'','', 'Services provided for menstrual regulation');
INSERT INTO layout_options VALUES ('LBFgcac','gc_methods'   ,'3IPPA CAC Section','Previous Method before MR'    ,19, 1,1, 0, 0,'gc_methods'   ,1,3,'','', 'Method prior to menstrual regulation');
INSERT INTO layout_options VALUES ('LBFgcac','gc_servstat'  ,'3IPPA CAC Section','Status of Services'           ,20, 1,1, 0, 0,'gc_servstat'  ,1,3,'','', 'Status of services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_refstat'   ,'3IPPA CAC Section','Referral Status'              ,21, 1,1, 0, 0,'gc_refstat'   ,1,3,'','', 'Referral status');
INSERT INTO layout_options VALUES ('LBFgcac','gc_pregweeks' ,'3IPPA CAC Section','Result of USG/Preg'           ,22, 2,1, 2, 2,''             ,1,3,'','', 'Number of weeks of pregnancy');
INSERT INTO layout_options VALUES ('LBFgcac','gc_rreason'   ,'3IPPA CAC Section','Reason when Rejected/Referred',23, 1,1, 0, 0,'gc_rreason'   ,1,3,'','', 'Reason for rejecting or referring services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_reason'    ,'3IPPA CAC Section','Main Reason for MR Services'  ,24, 1,1, 0, 0,'gc_reason'    ,1,3,'','', 'Main reason for requesting MR services');
INSERT INTO layout_options VALUES ('LBFgcac','gc_condition' ,'3IPPA CAC Section','Aborted Conception Condition' ,25, 1,1, 0 ,0,'gc_condition' ,1,3,'','', 'Condition of Aborted Conception');
INSERT INTO layout_options VALUES ('LBFgcac','gc_efforts'   ,'3IPPA CAC Section','Efforts Prior to Visit'       ,26, 1,1, 0, 0,'gc_efforts'   ,1,3,'','', 'Other efforts conducted before visiting the clinic');
INSERT INTO layout_options VALUES ('LBFgcac','gc_complaint' ,'3IPPA CAC Section','Complaint from Client'        ,27, 1,1, 0, 0,'gc_complaint' ,1,3,'','', 'Complaint from Client');

-- 15
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_howarrived';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_howarrived','GCAC How Arrived',88);
DELETE FROM list_options WHERE list_id = 'gc_howarrived';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_howarrived','SEN','Alone'         , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_howarrived','DS' ,'With Husband'  , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_howarrived','DK' ,'With Relative' , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_howarrived','DT' ,'With Friend'   , 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_howarrived','DP' ,'With Boyfriend', 5);

-- 16 and 17
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_typeserv';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_typeserv','GCAC Type of Services',88);
DELETE FROM list_options WHERE list_id = 'gc_typeserv';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','10' ,'IUD/AKDR'                                            , 10);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','11' ,'Pill'                                                , 11);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','12' ,'Condom'                                              , 12);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','13' ,'Vasectomy'                                           , 13);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','14' ,'Tubectomy'                                           , 14);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','15' ,'Injectable'                                          , 15);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','16' ,'Implant'                                             , 16);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','17' ,'Release IUD/Implant'                                 , 17);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','20' ,'Menstrual Regulation (less than 12 weeks)'           , 20);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','21' ,'Re-Menstrual Regulation (For the rest of conception)', 21);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','22' ,'MR Follow Up (Control One Week After Having MR)'     , 22);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','23' ,'Medical MR'                                          , 23);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','24' ,'Dilatasi and Curette (More than 12 weeks)'           , 24);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','30' ,'Pregnancy Control / USG'                             , 30);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','31' ,'Vagina Toilet'                                       , 31);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','32' ,'Callisthenic for Pregnant mother'                    , 32);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','33' ,'Callisthenic for  Mother after having delivery'      , 33);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','34' ,'STI treatment'                                       , 34);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','35' ,'Pap Smear'                                           , 35);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','36' ,'Vagino Plasti / Himenoplasti'                        , 36);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','40' ,'Infertility'                                         , 40);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','50' ,'Pregnancy Test'                                      , 50);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','51' ,'STI Lab'                                             , 51);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','52' ,'Sperms analysis'                                     , 52);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','53' ,'Pap Smear Test Lab'                                  , 53);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','54' ,'Other Lab Services'                                  , 54);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','60' ,'Immunization'                                        , 60);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','61' ,'General Health'                                      , 61);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','62' ,'Delivery'                                            , 62);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','63' ,'Circumcision'                                        , 63);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','70' ,'FP / Contraceptives Counselling'                     , 70);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','71' ,'Family Counselling'                                  , 71);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','72' ,'Adolescent Counselling'                              , 72);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','73' ,'STI / HIV Counselling'                               , 73);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_typeserv','99' ,'Not Know / Not Answer'                               , 99);

-- 18
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_menreg';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_menreg','GCAC For Menstrual Regulation',88);
DELETE FROM list_options WHERE list_id = 'gc_menreg';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','1' ,'Pre Counseling + MR + Post Counseling', 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','2' ,'Pre Counseling + MR + Post Counseling', 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','3' ,'Pre Counseling + MR + FP'             , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','4' ,'Pre Counseling + MR'                  , 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','5' ,'MR + FP without counseling'           , 5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','6' ,'MR without counseling'                , 6);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_menreg','7' ,'Counseling'                           , 7);

-- 19
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_methods';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_methods','GCAC Contraceptive Methods',88);
DELETE FROM list_options WHERE list_id = 'gc_methods';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','1' ,'None'                      , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','2' ,'Pill'                      , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','3' ,'Injectable'                , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','4' ,'Implant'                   , 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','5' ,'IUD'                       , 5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','6' ,'Vasectomy / Tubal Ligation', 6);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','7' ,'Condoms'                   , 7);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','8' ,'Natural'                   , 8);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_methods','9' ,'Others'                    , 9);

-- 20
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_servstat';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_servstat','GCAC Status of Services',88);
DELETE FROM list_options WHERE list_id = 'gc_servstat';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_servstat','DILA','Served'   , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_servstat','DITO','Rejected' , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_servstat','DITU','Postponed', 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_servstat','DIRU','Referred' , 4);

-- 21
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_refstat';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_refstat','GCAC Status of Services',88);
DELETE FROM list_options WHERE list_id = 'gc_refstat';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_refstat','1','IPPA'                  , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_refstat','2','Beyond IPPA'           , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_refstat','3','Others / Not FReferred', 3);

-- 23
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_rreason';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_rreason','GCAC Reason to Reject/Refer Services',88);
DELETE FROM list_options WHERE list_id = 'gc_rreason';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','1' ,'Service not available', 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','2' ,'Cost of service'      , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','3' ,'Single'               , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','4' ,'Medical reason'       , 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','5' ,'No responsible person', 5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_rreason','6' ,'Weeks of pregnancy'   , 6);

-- 24
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_reason';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_reason','GCAC Main Reason for MR Services',88);
DELETE FROM list_options WHERE list_id = 'gc_reason';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','1' ,'Have already enough children'                , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','2' ,'The children are still babies'               , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','3' ,'Too young to have baby'                      , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','4' ,'Too old to have other child'                 , 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','5' ,'Not / not yet married'                       , 5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','6' ,'Still goes to school / college'              , 6);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','7' ,'Engage with Official'                        , 7);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','8' ,'Could not stand pain / sickness of pregnancy', 8);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_reason','9' ,'Others'                                      , 9);

-- 25
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_condition';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_condition','GCAC Aborted Conception Condition',88);
DELETE FROM list_options WHERE list_id = 'gc_condition';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','1' ,'Fresh' , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','2' ,'Dark'  , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','3' ,'Sticky', 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','4' ,'Others', 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_condition','5' ,'N/A'   , 0);

-- 26
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_efforts';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_efforts','GCAC Prior Efforts',88);
DELETE FROM list_options WHERE list_id = 'gc_efforts';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','1' ,'None'                                         , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','2' ,'Drinking herbs / medicines'                   , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','3' ,'Had been taken care by paramedic'             , 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','4' ,'Massage / went to traditional birth attendant', 4);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','5' ,'Other efforts'                                , 5);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_efforts','6' ,'Emergency'                                    , 6);

-- 27
DELETE FROM list_options WHERE list_id = 'lists' AND option_id = 'gc_complaint';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('lists','gc_complaint','GCAC Complaint from Client',88);
DELETE FROM list_options WHERE list_id = 'gc_complaint';
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','1' ,'Facility'         , 1);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','2' ,'Time of Queue'    , 2);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','3' ,'Charge of Service', 3);
INSERT INTO list_options ( list_id, option_id, title, seq ) VALUES ('gc_complaint','4' ,'None'             , 4);

