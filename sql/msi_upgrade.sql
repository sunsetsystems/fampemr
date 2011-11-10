#IfNotIndex forms encounter
CREATE INDEX `encounter` ON `forms` (`encounter`);
#EndIf

#IfNotIndex drug_sales encounter
CREATE INDEX `encounter` ON `drug_sales` (`encounter`);
#EndIf

#IfNotIndex billing encounter
CREATE INDEX `encounter` ON `billing` (`encounter`);
#EndIf

