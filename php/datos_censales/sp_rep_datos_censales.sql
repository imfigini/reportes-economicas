DROP FUNCTION 'dba'.sp_rep_datos_censales();

CREATE PROCEDURE  "dba".sp_rep_datos_censales()
RETURNING      
	DATETIME YEAR to FRACTION	--fecha en que se corre este SP  

DEFINE v_fecha_actualizacion DATETIME YEAR to FRACTION;

BEGIN

{
DROP TABLE rep_datos_censales;
CREATE TABLE rep_datos_censales
(
	nro_inscripcion VARCHAR(10),
	alumno VARCHAR(60),
	nro_documento VARCHAR(15),
	alu_obra_social VARCHAR(100),
	cant_hijos_alum VARCHAR(100),
	alu_costea_estudios VARCHAR(100),
	alu_tipo_beca VARCHAR(100),
	alu_beca_eco_tran VARCHAR(1),
	alu_beca_eco_come VARCHAR(1),
	alu_beca_eco_foto VARCHAR(1),
	alu_beca_eco_efec VARCHAR(1), 
	alu_beca_eco_habi VARCHAR(1),
	alu_cos_est_espec VARCHAR(100),
	existe_trab_alum VARCHAR(100),
	alu_trab_ocup VARCHAR(100), 
	hora_sem_trab_alum VARCHAR(100),
	localidad_secundario VARCHAR(100),
	anio_ingreso INTEGER
);
}

DELETE FROM rep_datos_censales;

INSERT INTO rep_datos_censales
SELECT DISTINCT 
	P.nro_inscripcion, 
	P.apellido || ', ' || P.nombres AS alumno,
	P.nro_documento,
	CASE 	WHEN D3.alu_obra_social = 1 THEN 'Por ser familiar a cargo (de padre, madre, conyuge o tutor)'
		WHEN D3.alu_obra_social = 2 THEN 'Por su propio trabajo'
		WHEN D3.alu_obra_social = 3 THEN 'Como afiliado voluntario (a obra social o prepaga)'
		WHEN D3.alu_obra_social = 4 THEN 'Otorgada por la universidad (por ser estudiante)'
		WHEN D3.alu_obra_social = 5 THEN 'Carece de cobertura de salud'
		ELSE ''
	END AS alu_obra_social,
	CASE	WHEN D2.cant_hijos_alum = 0 THEN 'No tiene'
		WHEN D2.cant_hijos_alum = 1 THEN 'Uno'
		WHEN D2.cant_hijos_alum = 2 THEN 'Dos'
		WHEN D2.cant_hijos_alum = 3 THEN 'Mas de dos'
		ELSE ''
	END AS cant_hijos_alum,
	CASE 	WHEN D3.alu_cos_est_ap_fam = 'S' THEN 'Con el aporte de familiares'
		WHEN D3.alu_cos_est_beca = 'S' THEN 'Con su beca'
		WHEN D3.alu_cos_est_plsoc = 'S' THEN 'Planes sociales'
		WHEN D3.alu_cos_est_trab = 'S' THEN 'Con su trabajo'
		WHEN D3.alu_cos_est_otra = 'S' THEN 'Otra'
		ELSE ''
	END AS alu_costea_estudios,
	CASE 	WHEN D3.alu_beca_tipo_eco = 'S' THEN 'De ayuda económica'
		WHEN D3.alu_beca_tipo_ser = 'S' THEN 'De contraprestación de servicios'
		WHEN D3.alu_beca_tipo_inv = 'S' THEN 'De investigación'
		WHEN D2.tiene_beca = 'N' THEN 'No tiene beca'
		ELSE ''
	END AS alu_tipo_beca,
	D3.alu_beca_eco_tran, --'Transporte'
	D3.alu_beca_eco_come, --'Comedor'
	D3.alu_beca_eco_foto, --'Fotocopia'
	D3.alu_beca_eco_efec, --'Efectivo'
	D3.alu_beca_eco_habi, --'Habitacional'
	D3.alu_cos_est_espec, --'Otra fuente de ayuda'
	CASE 	WHEN D2.existe_trab_alum = 3 THEN 'No trabajó y no buscó trabajo (no esta pensando en trabajar)'
		WHEN D2.existe_trab_alum = 2 THEN 'No trabajó y buscó trabajo en algún momento de los últimos 30 días'
		WHEN D2.existe_trab_alum = 1 THEN 'Trabajó al menos una hora (incluye ausencia por licencia, vacaciones, enfermedad)'
		ELSE ''
	END AS existe_trab_alum,
	CASE 	WHEN D3.alu_trab_ocup = 1 THEN 'Permanente (incluye fijo, estable, de planta)'
		WHEN D3.alu_trab_ocup = 2 THEN 'Temporaria (incluye changa, trabajo o estacional, contratado, suplencia, etc.)'
		ELSE ''
	END AS alu_trab_opcup,
	CASE 	WHEN D.hora_sem_trab_alum = 1 THEN 'Hasta 10 horas'
		WHEN D.hora_sem_trab_alum = 2 THEN 'Mas de 10 y hasta 20 horas'
		WHEN D.hora_sem_trab_alum = 3 THEN 'Mas de 20 y menos de 35 horas'
		WHEN D.hora_sem_trab_alum = 4 THEN '35 o mas horas'
		ELSE ''
	END AS hora_sem_trab_alum,
	L.nombre AS localidad_secundario,
	MIN(PI.anio_academico) AS anio_ingreso
FROM sga_alumnos A
JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion AND A.regular = 'S' AND A.calidad = 'A')
JOIN sga_carrera_aspira CA ON (CA.unidad_academica = A.unidad_academica AND CA.carrera = A.carrera AND CA.nro_inscripcion = A.nro_inscripcion)
JOIN sga_periodo_insc PI ON (PI.periodo_inscripcio = CA.periodo_inscripcio)
LEFT JOIN vw_datos_censales_actuales D ON (P.nro_inscripcion = D.nro_inscripcion)
LEFT JOIN vw_datos_cen_aux_actuales D2 ON (D2.nro_inscripcion = P.nro_inscripcion)
LEFT JOIN sga_datos_cen_aux2 D3 ON (D3.nro_inscripcion = P.nro_inscripcion)
LEFT JOIN sga_coleg_sec CS ON (CS.colegio = P.colegio_secundario)
LEFT JOIN mug_localidades L ON (L.localidad = CS.localidad)
WHERE D3.fecha_relevamiento = (
	      SELECT MAX(x1.fecha_relevamiento) 
	      FROM sga_datos_cen_aux2 x1 
	      WHERE x1.unidad_academica = D3.unidad_academica AND x1.nro_inscripcion = D3.nro_inscripcion
    	)
GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17;

SELECT CURRENT 
	INTO v_fecha_actualizacion
	FROM systables WHERE tabid == 1;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_datos_censales', v_fecha_actualizacion);
RETURN v_fecha_actualizacion;

END;	

END PROCEDURE;

--EXECUTE PROCEDURE sp_rep_datos_censales();