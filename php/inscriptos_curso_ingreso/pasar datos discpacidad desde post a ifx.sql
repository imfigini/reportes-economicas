--Para pasar los datos censales desde Postgres a Mini Guarani, referidos a discapacidad y cómo se enteró de la carrera
--Desde mini guarani tomar todos los inscriptos reales
SELECT DISTINCT "INSERT INTO aux_inscriptos VALUES ('" || nro_documento || "');"
	FROM sga_personas A, sga_alumnos B
	WHERE A.nro_inscripcion = B.nro_inscripcion AND B.fecha_ingreso > MDY(8,1,2015);

--Con ese listado de DNI ir a la base de preinscripción (Postgres) y:
CREATE TABLE aux_inscriptos
(	nro_doc character varying(15) NOT NULL,
	CONSTRAINT aux_inscriptos_pkey PRIMARY KEY (nro_doc)
);
--y ejecutar el resultado de la consulta hecha en el mini guarani. 

--Verificar qué Documentos que no coinciden para verlos manualmente
SELECT * FROM sga_preinscripcion 
	WHERE nro_documento NOT IN (SELECT nro_doc FROM aux_inscriptos)
	AND estado = 'I';

--En Postgres ejecutar la consulta siguiente grabándola en archivo
SELECT DISTINCT A.nro_documento,
	A.discapacidad_leer,
	A.discapacidad_oir,
	A.discapacidad_caminar,
	A.discapacidad_agarrar,
	A.discapacidad_especificar,
	A.como_te_enteraste,
	A.como_te_enteraste_pagina,
	A.como_te_enteraste_otros,
	A.como_te_enteraste_otro_motivo,
	A.participaste_evento,
	A.como_te_enteraste_cual,
	A.como_te_enteraste_cuando,
	A.como_te_enteraste_donde
FROM sga_preinscripcion A, aux_inscriptos B
	WHERE A.nro_documento = B.nro_doc;

--El archivo CVS que se genere, importar datos desde texto con EXCEL.
--Remplazar las celdas vacías por NULL
--En la celda P1--> INSERT INTO sga_datos_cen_aux3 VALUES (
--=CONCATENAR($P$1;A2;",";B2;",";C2;",";D2;",";E2;",";F2;",";G2;",";H2;",";I2;",";J2;",";K2;",";L2;",";M2;",";N2;");")
	
--En el mini Guarani crear si no existe la siguiente tabla para que almacene los datos censales extra. 
--DROP TABLE 'dba'.sga_datos_cen_aux3;
CREATE TABLE 'dba'.sga_datos_cen_aux3
(	nro_documento VARCHAR(10),
	discapacidad_leer VARCHAR(1),
	discapacidad_oir VARCHAR(1),
	discapacidad_caminar VARCHAR(1),
	discapacidad_agarrar VARCHAR(1),
	discapacidad_especificar VARCHAR(100),
	como_te_enteraste INT,
	como_te_enteraste_pagina INT,
	como_te_enteraste_otros VARCHAR(100),
	como_te_enteraste_otro_motivo VARCHAR(100),
	participaste_evento VARCHAR(1),
	como_te_enteraste_cual VARCHAR(100),
	como_te_enteraste_cuando VARCHAR(100),
	como_te_enteraste_donde VARCHAR(100)
);
ALTER TABLE 'dba'.sga_datos_cen_aux3 ADD CONSTRAINT PRIMARY KEY (nro_documento);

--Importar todos los datos del EXCEL.
--Con esto ya se tendrían todos los datos censales extra cargados en el Mini Guarani para poder hacer las consultas pertinentes. 

--Si no existe, crear el procedimiento "sp_getDatosAlumnos.sql" que va a servir para las consultas php



