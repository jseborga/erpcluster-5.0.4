ALTER TABLE llx_p_holiday ADD UNIQUE uk_unique (entity, ref);
ALTER TABLE llx_p_holiday ADD UNIQUE uk_unique_date (entity, date_day, date_month, date_year);