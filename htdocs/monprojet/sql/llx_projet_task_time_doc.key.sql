ALTER TABLE llx_projet_task_time_doc ADD UNIQUE uk_unique(fk_task_time);
ALTER TABLE llx_projet_task_time_doc ADD fk_task_payment integer NULL DEFAULT '0' AFTER fk_task_time;
ALTER TABLE llx_projet_task_time_doc ADD fk_user_mod integer NULL DEFAULT '0' AFTER date_create;
ALTER TABLE llx_projet_task_time_doc ADD date_mod date NULL AFTER tms;
ALTER TABLE llx_projet_task_time_doc CHANGE document document VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE llx_projet_task_time_doc ADD fk_request_item integer NULL DEFAULT '0' AFTER fk_task_payment;

