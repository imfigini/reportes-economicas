DROP PROCEDURE 'dba'.sp_getdatosinscriptos();
DROP PROCEDURE 'dba'.sp_getdatosinscriptos(char);

CREATE PROCEDURE "dba".sp_getdatosinscriptos(pAnio LIKE sga_carrera_aspira.periodo_inscripcio)

BEGIN
{
	DROP TABLE rep_datos_inscriptos;
	CREATE TABLE rep_datos_inscriptos
	(	nro_inscripcion VARCHAR(15),
		inscripto VARCHAR(60), 
		periodo_inscripcio VARCHAR(20),
		nro_doc	VARCHAR(15), 
		fecha_nacim date,
		edad INTEGER,
		colegio VARCHAR(100), 
		loc_colegio VARCHAR(100), 
		e_mail VARCHAR(50), 
		celular VARCHAR(18),
		loc_proced VARCHAR(100), 
		direc_proced VARCHAR(56), 
		obra_social VARCHAR(28),
		situacion_laboral VARCHAR(37),
		remuneracion VARCHAR(2),
		hora_sem_trab_alum VARCHAR(16),
		rel_trab_carrera VARCHAR(40),
		cant_fami_cargo INTEGER,
		existe_trab_alum VARCHAR(100),
		cant_hijos_alum	INTEGER, 
		vive_actual_con VARCHAR(100),
		tiene_beca VARCHAR(2),
		practica_deportes VARCHAR(2),
		costea_est_con_aporte_fliares VARCHAR(2),
		costea_est_con_beca VARCHAR(2),
		costea_est_con_plan_social VARCHAR(2),
		costea_est_con_su_trabajo VARCHAR(2),
		costea_est_con_otra_fuente VARCHAR(2),
		sit_laboral_padre VARCHAR(30),
		ult_est_cur_padre VARCHAR(30),
		sit_laboral_madre VARCHAR(30),
		ult_est_cur_madre VARCHAR(30),
		idioma_ingles VARCHAR(15),
		como_te_enteraste VARCHAR(20),
		como_te_enteraste_pagina VARCHAR(30),
		como_te_enteraste_otros VARCHAR(100),
		participaste_evento VARCHAR(2),
		como_te_enteraste_cual VARCHAR(100),
		como_te_enteraste_donde VARCHAR(100),
		como_te_enteraste_cuando VARCHAR(100),
		como_te_enteraste_otro_motivo VARCHAR(100),
		discapacidad_leer VARCHAR(2),
		discapacidad_oir VARCHAR(2),
		discapacidad_caminar VARCHAR(2),
		discapacidad_agarrar VARCHAR(2),
		discapacidad_especificar VARCHAR(100),
		sexo integer
	);
}

	DELETE FROM rep_datos_inscriptos 
		WHERE periodo_inscripcio = pAnio;

	INSERT INTO rep_datos_inscriptos
					SELECT DISTINCT A.nro_inscripcion,
						B.apellido || ', ' || B.nombres AS inscripto, 
						S.periodo_inscripcio,
						B.nro_documento, 
						B.fecha_nacimiento,
						((TODAY-B.fecha_nacimiento)/365)::INTEGER AS edad,
						C.nombre AS colegio, 
						F.nombre AS loc_colegio, 
						D.e_mail, 
						H.celular_numero,
						E.nombre AS loc_proced, 
						D.calle_proc || ' ' || D.numero_proc AS direc_proced, 
						CASE 	WHEN G.alu_obra_social=1 THEN 'Por ser familiar a cargo'
							WHEN G.alu_obra_social=2 THEN 'Por su propio trabajo'
							WHEN G.alu_obra_social=3 THEN 'Como afiliado voluntario'
							WHEN G.alu_obra_social=4 THEN 'Otorgada por la univ.'
							WHEN G.alu_obra_social=5 THEN 'Carece de cobertura de salud'
						END AS obra_social,
						CASE 	WHEN H.existe_trab_alum=1 THEN 'Trabajó al menos 1 h la última semama'
							WHEN H.existe_trab_alum=2 THEN 'No trabajó y buscó'
							WHEN H.existe_trab_alum=3 THEN 'No trabajó y no buscó'
							ELSE ''
						END AS situacion_laboral,
						CASE 	WHEN H.remuneracion='S' THEN 'Si'
							WHEN H.remuneracion='N' THEN 'No'
							ELSE ''
						END AS remuneracion,
						CASE 	WHEN D.hora_sem_trab_alum=1 THEN 'Hasta 10 hs'
							WHEN D.hora_sem_trab_alum=2 THEN 'Entre 10 y 20 hs'
							WHEN D.hora_sem_trab_alum=3 THEN 'Entre 20 y 35 hs'
							WHEN D.hora_sem_trab_alum=4 THEN 'Mas de 35 hs'
							ELSE ''
						END AS hora_sem_trab_alum,
						J.descripcion AS rel_trab_carrera,
						H.cant_fami_cargo,
						CASE 	WHEN H.existe_trab_alum=1 THEN 'Trabajó al menos una hora (incluye ausencia por licencia, vacaciones, enfermedad)'
							WHEN H.existe_trab_alum=2 THEN 'No trabajó y buscó trabajo en algún momento de los últimos 30 días'
							WHEN H.existe_trab_alum=3 THEN 'No trabajó y no buscó trabajo (no esta pensando en trabajar)'
							ELSE ''
						END AS existe_trab_alum,
						H.cant_hijos_alum,
						CASE 	WHEN H.vive_actual_con=1 THEN 'Solo'
							WHEN H.vive_actual_con=2 THEN 'Con compañeros'
							WHEN H.vive_actual_con=3 THEN 'Con familia de origen (padres, hermanos, abuelos)'
							WHEN H.vive_actual_con=4 THEN 'Con su pareja/hijos'
							WHEN H.vive_actual_con=5 THEN 'Otros'
							ELSE ''
						END AS vive_actual_con,
						CASE 	WHEN H.tiene_beca='S' THEN 'Si'
							WHEN H.tiene_beca='N' THEN 'No'
							ELSE ''
						END AS tiene_beca,
						CASE 	WHEN H.practica_deportes='S' THEN 'Si'
							WHEN H.practica_deportes='N' THEN 'No'
							ELSE ''
						END AS practica_deportes,
						CASE 	WHEN G.alu_cos_est_ap_fam='S' THEN 'Si'
							WHEN G.alu_cos_est_ap_fam='N' THEN 'No'
							ELSE ''
						END AS alu_cos_est_ap_fam,
						CASE 	WHEN G.alu_cos_est_beca='S' THEN 'Si'
							WHEN G.alu_cos_est_beca='N' THEN 'No'
							ELSE ''
						END AS alu_cos_est_plsoc,
						CASE 	WHEN G.alu_cos_est_plsoc='S' THEN 'Si'
							WHEN G.alu_cos_est_plsoc='N' THEN 'No'
							ELSE ''
						END AS alu_cos_est_plsoc,
						CASE 	WHEN G.alu_cos_est_trab='S' THEN 'Si'
							WHEN G.alu_cos_est_trab='N' THEN 'No'
							ELSE ''
						END AS alu_cos_est_trab,
						CASE 	WHEN G.alu_cos_est_otra='S' THEN 'Si'
							WHEN G.alu_cos_est_otra='N' THEN 'No'
							ELSE ''
						END AS alu_cos_est_otra,
						K.descirpcion AS sit_laboral_padre,
						L.descripcion AS ult_est_cur_padre,
						M.descirpcion AS sit_laboral_madre,
						N.descripcion AS ult_est_cur_madre,
						CASE 	WHEN G.alu_idioma_ingl = 1 THEN 'Muy bueno'
							WHEN G.alu_idioma_ingl = 2 THEN 'Bueno'	
							WHEN G.alu_idioma_ingl = 3 THEN 'Básico'
							WHEN G.alu_idioma_ingl = 4 THEN 'Desconoce'
							ELSE ''
						END AS idioma_ingles,
						CASE 	WHEN I.como_te_enteraste = 1 THEN 'Buscando en Internet'
							WHEN I.como_te_enteraste = 2 THEN 'Amigo o conocido'	
							WHEN I.como_te_enteraste = 3 THEN 'Por colegio/escuela'
							WHEN I.como_te_enteraste = 4 THEN 'Otro'
							ELSE ''
						END AS como_te_enteraste,
						CASE 	WHEN I.como_te_enteraste_pagina = 1 THEN 'www.exa.unicen.edu.ar'
							WHEN I.como_te_enteraste_pagina = 2 THEN 'facebook'	
							WHEN I.como_te_enteraste_pagina = 3 THEN 'www.portaldelestudiante.gov.ar'
							WHEN I.como_te_enteraste_pagina = 4 THEN 'www.estudiarcomputacion.gob.ar'
							WHEN I.como_te_enteraste_pagina = 5 THEN 'Otra'
							ELSE ''
						END AS como_te_enteraste_pagina,
						I.como_te_enteraste_otros,
						CASE 	WHEN I.participaste_evento = 'S' THEN 'Si'
							WHEN I.participaste_evento = 'N' THEN 'No'	
							ELSE ''
						END AS participaste_evento,
						I.como_te_enteraste_cual,
						I.como_te_enteraste_donde,
						I.como_te_enteraste_cuando,
						I.como_te_enteraste_otro_motivo,
						CASE 	WHEN I.discapacidad_leer = 'S' THEN 'Si'
							WHEN I.discapacidad_leer = 'N' THEN 'No'	
							ELSE ''
						END AS discapacidad_leer,
						CASE 	WHEN I.discapacidad_oir = 'S' THEN 'Si'
							WHEN I.discapacidad_oir = 'N' THEN 'No'	
							ELSE ''
						END AS discapacidad_oir,
						CASE 	WHEN I.discapacidad_caminar = 'S' THEN 'Si'
							WHEN I.discapacidad_caminar = 'N' THEN 'No'	
							ELSE ''
						END AS discapacidad_caminar,
						CASE 	WHEN I.discapacidad_agarrar = 'S' THEN 'Si'
							WHEN I.discapacidad_agarrar = 'N' THEN 'No'	
							ELSE ''
						END AS discapacidad_agarrar,
						I.discapacidad_especificar,
						B.sexo

					FROM 	sga_alumnos A 
						LEFT JOIN sga_personas B ON (A.nro_inscripcion = B.nro_inscripcion)
						LEFT JOIN sga_carrera_aspira S ON (A.nro_inscripcion = S.nro_inscripcion AND A.carrera = S.carrera)
						LEFT JOIN sga_coleg_sec C ON (B.colegio_secundario = C.colegio)
						LEFT JOIN mug_localidades F ON (F.localidad = C.localidad)
						LEFT JOIN vw_datos_censales_actuales D ON (D.nro_inscripcion = A.nro_inscripcion)
						LEFT JOIN sga_rel_trab_carr J ON (D.rel_trab_carrera = J.rel_trab_carrera)
						LEFT JOIN sga_sit_laboral K ON (D.sit_laboral_padre	= K.situacion_laboral)
						LEFT JOIN sga_tipos_est_curs L ON (D.ult_est_cur_padre = L.estudio_cur)
						LEFT JOIN sga_sit_laboral M ON (D.sit_laboral_madre	= M.situacion_laboral)
						LEFT JOIN sga_tipos_est_curs N ON (D.ult_est_cur_madre = N.estudio_cur)
						LEFT JOIN mug_localidades E ON (D.loc_proc = E.localidad)
						LEFT JOIN sga_datos_cen_aux2 G ON (G.unidad_academica = A.unidad_academica AND G.nro_inscripcion = A.nro_inscripcion
								AND G.fecha_relevamiento = (SELECT MAX(GG.fecha_relevamiento) FROM sga_datos_cen_aux2 GG WHERE G.unidad_academica = GG.unidad_academica AND G.nro_inscripcion = GG.nro_inscripcion))
						LEFT JOIN sga_datos_cen_aux H ON (H.unidad_academica = A.unidad_academica AND H.nro_inscripcion = A.nro_inscripcion
								AND H.fecha_relevamiento = (SELECT MAX(HH.fecha_relevamiento) FROM sga_datos_cen_aux HH WHERE H.unidad_academica = HH.unidad_academica AND H.nro_inscripcion = HH.nro_inscripcion))
						LEFT JOIN sga_datos_cen_aux3 I ON (I.nro_documento LIKE B.nro_documento)
						WHERE 	A.carrera <> 211 --que no sea LEM
							AND S.periodo_inscripcio = pAnio; 

END;

END PROCEDURE;
