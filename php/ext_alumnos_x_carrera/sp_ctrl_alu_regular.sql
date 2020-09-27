-- DROP PROCEDURE "dba".sp_ctrl_alu_regular;

CREATE PROCEDURE "dba".sp_ctrl_alu_regular(
	pUnidadAcademica LIKE sga_alumnos.unidad_academica,
	pCarrera LIKE sga_alumnos.carrera,
	pAnioAcademico INTEGER,
	pLegajo LIKE sga_alumnos.legajo)

	RETURNING integer;

	-- Variables locales
	DEFINE iCantidad      integer;
	DEFINE iAnioIngreso INTEGER;
	
	DEFINE vFechaInicio       LIKE sga_anio_academico.fecha_inicio;
	DEFINE vFechaFin          LIKE sga_anio_academico.fecha_fin;

	-- Variables para el manejo de excepciones
	DEFINE SQLErr             integer;
	DEFINE ISAMError          integer;
	DEFINE errorInfo          varchar(76);

	-- Si hay algun error
	ON EXCEPTION SET SQLErr, ISAMError, errorInfo                                        
		RAISE EXCEPTION SQLErr, ISAMError, errorInfo;
	END EXCEPTION;

BEGIN
	LET iCantidad = 0;
	LET iAnioIngreso = 0;

	-- Recupero el año de ingreso, para ver si es ingresante	
	FOREACH	
		SELECT DISTINCT sga_periodo_insc.anio_academico
		INTO iAnioIngreso
		FROM sga_alumnos, sga_carrera_aspira, sga_periodo_insc
		WHERE 
			sga_alumnos.carrera = sga_carrera_aspira.carrera AND
			sga_alumnos.nro_inscripcion = sga_carrera_aspira.nro_inscripcion AND
			sga_carrera_aspira.periodo_inscripcio = sga_periodo_insc.periodo_inscripcio AND	
			sga_alumnos.legajo = pLegajo AND
			sga_alumnos.carrera = pCarrera AND
			sga_alumnos.unidad_academica = pUnidadAcademica
		BEGIN
			-- Si es ingresante, sale sin error
			IF iAnioIngreso = pAnioAcademico THEN
				RETURN 1;
			END IF;
		END;
	END FOREACH;
		
	-- Cuento las reinscripciones en el año
	SELECT COUNT(*)
	INTO iCantidad
	FROM sga_reinscripcion
		WHERE 
			anio_academico = pAnioAcademico AND
			legajo = pLegajo AND
			carrera = pCarrera AND
			unidad_academica = pUnidadAcademica;
	
	-- Si no tiene reinscripcion devuelve error
	IF iCantidad = 0 THEN
		RETURN -1;
	END IF;

	LET iCantidad = 0;
	-- Verifico si ya era egresado de carrera de grado en ese año
	SELECT COUNT(*)
	INTO iCantidad
		FROM sga_alumnos A, sga_titulos_otorg O, sga_titulos T
		WHERE 	A.legajo = pLegajo
			AND A.carrera = pCarrera
			AND A.unidad_academica = pUnidadAcademica
			AND A.unidad_academica = O.unidad_academica 
			AND A.nro_inscripcion = O.nro_inscripcion 
			AND A.carrera = O.carrera
			AND O.unidad_academica = T.unidad_academica 
			AND O.titulo = T.titulo 
			AND T.nivel = 'GRADO'
			AND YEAR(O.fecha_egreso) < pAnioAcademico;
	
	-- Si ya era egrasado en ese año
	IF iCantidad > 0 THEN
		RETURN -1;
	END IF;

	RETURN 1;
END;
END PROCEDURE;

--EXECUTE PROCEDURE sp_ctrl_alu_regular('EXA', 206, 2010, '243095')
