-------------------------En SIU-guarani--------------------------------------------------
--DROP TABLE curso_ingreso;
CREATE TABLE curso_ingreso
(	anio_ingreso INTEGER,
	apellido VARCHAR(30) not null,
	nombres VARCHAR(30) not null,
	tipo_documento VARCHAR(3),
	nro_documento VARCHAR(15) not null,
	fecha_de_examen DATE not null,
	forma_aprobacion VARCHAR(25),
	resultado VARCHAR(2),
	nota VARCHAR(5)
);

CREATE INDEX idx_curso_ingreso_anio ON curso_ingreso(anio_ingreso);
CREATE INDEX idx_curso_ingreso_apellido ON curso_ingreso(apellido);
CREATE INDEX idx_curso_ingreso_fecha ON curso_ingreso(fecha_de_examen);

SELECT * FROM curso_ingreso;

-------------------DESDE MINI GUARANI------------------------------------------------------
CREATE SYNONYM CURSO FOR siu_guarani@ol_guarani2:curso_ingreso;
INSERT INTO CURSO
SELECT I.anio_academico, P.apellido, P.nombres, D.desc_abreviada AS tipo_documento, P.nro_documento, V.fecha, V.forma_aprobacion, V.resultado, V.nota
	FROM vw_hist_academica V
		JOIN sga_alumnos A ON (V.unidad_academica = A.unidad_academica AND V.carrera = A.carrera AND V.legajo = A.legajo)
		JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
		JOIN sga_carrera_aspira C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera AND C.nro_inscripcion = A.nro_inscripcion)
		JOIN sga_periodo_insc I ON (C.periodo_inscripcio = I.periodo_inscripcio)
		LEFT JOIN mdp_tipo_documento D ON (P.tipo_documento = D.tipo_documento)
	WHERE resultado IN ('A', 'P');
