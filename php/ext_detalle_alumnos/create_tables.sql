CREATE TABLE rep_extensiones_cursada
(
	carrera VARCHAR(5),
	materia VARCHAR(5),
	legajo VARCHAR(15),
	f_venc_reg_ant DATE,
	f_prorroga_hasta DATE,
	fecha_alta DATE,
	resultado_final VARCHAR(2),
	fecha_final DATE
);
CREATE CLUSTER INDEX idx_rep_extensiones_cursada ON rep_extensiones_cursada(fecha_alta);

CREATE TABLE rep_fecha_actualiz_tablas
(	tabla VARCHAR(100),
	fecha_ultima_actualizacion DATETIME YEAR TO SECOND
);