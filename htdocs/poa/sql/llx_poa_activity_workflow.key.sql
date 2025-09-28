ALTER TABLE llx_poa_activity_workflow ADD code_area_last TEXT NULL AFTER fk_activity;
ALTER TABLE llx_poa_activity_workflow ADD code_area_next TEXT NULL AFTER code_area_last;
ALTER TABLE llx_poa_activity_workflow ADD code_procedure TEXT NULL AFTER code_area_next;
ALTER TABLE llx_poa_activity_workflow ADD doc_verif TEXT NULL AFTER code_procedure;
