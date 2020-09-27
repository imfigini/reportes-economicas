<?php
require_once('MisConsultas.php');
class consultas_extension
{
	//Retorna los a�os acad�micas a partir del 2010
	static function get_anios_academicos()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT anio_academico, anio_academico FROM sga_periodo_insc WHERE anio_academico >= 2010 ORDER BY 1 DESC";
		
		$anios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $anios;
	}

	//Retorna las carreras de la unidad acad�mica
	function get_carreras()
	{
                //Descarta las carreras LEM y cursos Extracurriculares
                //Descarta las diplomaturas que se gestionan desde otra base
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT carrera, nombre 
					FROM sga_carreras 
					WHERE carrera NOT IN (211,290,214,215)  
				ORDER BY nombre";  
		
		$carreras = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $carreras;
	}
	
	//Retorna los a�os acad�micos posteriores a un a�o en particular
	function get_anios_academicos_posteriores($anio)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT anio_academico FROM sga_periodo_insc WHERE anio_academico > $anio ORDER BY 1 ASC";
		$anios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $anios;
	}
	
	function get_anios_academicos_ingreso()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT anio_academico, anio_academico FROM sga_periodo_insc WHERE anio_academico >= 2014 ORDER BY 1 DESC";
		
		$anios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $anios;
	}
	
	function get_nombres_cuatrimestre()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT periodo_lectivo, periodo_lectivo FROM sga_per_lect_gen WHERE tipo_de_periodo = 'Cuatrimestral'";
		
		$periodos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $periodos;
	}
	
	function get_materias_primero($cuatrimestre)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT materia, nombre_materia FROM sga_atrib_mat_plan A, sga_carreras C
			WHERE C.carrera = 206 
			AND A.carrera = C.carrera
			AND A.plan = C.plan_vigente
			AND A.anio_de_cursada = 1
			AND A.periodo_dictado = '$cuatrimestre'
			ORDER BY nombre_materia";

		$materias = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $materias;
	}
	
	function get_recursadas_primero()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT A.materia, A.nombre_materia FROM sga_atrib_mat_plan A, sga_carreras C, sga_comisiones B
			WHERE C.carrera = 206 
			AND A.carrera = C.carrera
			AND A.plan = C.plan_vigente
			AND A.anio_de_cursada = 1
			AND A.periodo_dictado = '1� cuatrimestre'
			AND B.materia = A.materia
			AND B.periodo_lectivo = '2� cuatrimestre'
			ORDER BY A.nombre_materia";

		$materias = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $materias;
	}
	
	//Retorna los alumnos de cohorte que cursaron una determinada materia (S�lo carrera 206)
	function get_alumnos_cohorte_cursada($materia, $anio, $cuatrimestre, $condicion)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT A.legajo, D.apellido || ', ' || D.nombres AS alumno, A.fecha_regularidad, A.resultado, A.nota
					FROM sga_cursadas A
					JOIN sga_carrera_aspira B ON (A.unidad_academica = B.unidad_academica AND A.carrera = B.carrera AND A.legajo = B.nro_inscripcion)
					JOIN sga_comisiones C ON (A.unidad_academica = C.unidad_academica AND C.comision = A.comision AND C.anio_academico = $anio)
					JOIN sga_personas D ON (D.unidad_academica = B.unidad_academica AND D.nro_inscripcion = B.nro_inscripcion)
					WHERE A.carrera = 206
					AND A.materia = '$materia'
					AND B.periodo_inscripcio = 'I-$anio'
					AND C.periodo_lectivo LIKE '$cuatrimestre%'";

		switch ($condicion) {
			case 'Aprobados':
				$resultado = 'A';
				break;
			case 'Promocionados':
				$resultado = 'P';
				break;
			case 'Desaprobados':
				$resultado = 'R';
				break;
			case 'Ausentes':
				$resultado = 'U';
				break;
			default: 
				$resultado = 'T';
		}

		if ($resultado <> 'T')
		{
			$sql .= " AND A.resultado = '$resultado'";
		}
		
		$sql .= " ORDER BY alumno";

		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$alumnos_cohorte = consultas_extension::filtrar_alumnos_cohorte($alumnos, 206);
		return $alumnos_cohorte;
	}
		
	//Descrata los alumnos que no son de cohorte
	function filtrar_alumnos_cohorte($alumnos, $carrera)
	{
		$db = MisConsultas::getConexion ();
		$max = count($alumnos);
		for ($i=0; $i<$max; $i++)
		{
			$alumno = $alumnos[$i]['LEGAJO'];
			$sql = "EXECUTE PROCEDURE dba.sp_alu_cohorte($alumno, $carrera)";
			$es_cohorte = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
			if (!$es_cohorte[0][0])
				unset($alumnos[$i]);
		}
		return $alumnos;
	}
	
	//Retorna los alumnos de cohorte que ingresaron un determinado a�o en una determinada carrera
	function get_alumnos_ingresantes_cohorte($filtro=array())
	{
		$where = array();
		if (isset($filtro['ANIO_ACADEMICO'])) 
		{	$anio = $filtro['ANIO_ACADEMICO'];
			$where[] = " R.anio_ingreso = $anio";
		}
		if (isset($filtro['NOMBRE_CARRERA'])) 
		{
			$carrera = $filtro['NOMBRE_CARRERA'];
			$where[] = " R.carrera = $carrera";
		}

		$sql = "SELECT R.legajo, R.alumno, R.carrera, R.nombre_carrera, R.anio_ingreso,
                                DECODE (A.calidad, 'A', 'Activo', 'E', 'Egresado', 'P', 'Pasivo', 'N', 'Abandon�') AS calidad,
                                DECODE (P.sexo, 1, 'Masc', 2, 'Fem') AS sexo,
                                S.nombre AS sede
                        FROM rep_alumnos_cohorte R
                        JOIN sga_alumnos A ON (A.legajo = R.legajo AND A.carrera = R.carrera)
                        JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
                        JOIN sga_sedes S ON (S.sede = A.sede)
                                WHERE R.carrera NOT IN (211,290) ";
		
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$sql .= " ORDER BY R.anio_ingreso, R.nombre_carrera, R.alumno";

		$db = MisConsultas::getConexion ();
		$alumnos_cohorte = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $alumnos_cohorte;
	}
	
    //Retorna los alumnos que NO son de cohorte que ingresaron un determinado a�o en una determinada carrera
	function get_alumnos_ingresantes_no_cohorte($filtro=array())
	{
		$where = array();
		if (isset($filtro['ANIO_ACADEMICO'])) 
		{	$anio = $filtro['ANIO_ACADEMICO'];
			$where[] = " I.anio_academico = $anio";
		}
		if (isset($filtro['NOMBRE_CARRERA'])) 
		{
			$carrera = $filtro['NOMBRE_CARRERA'];
			$where[] = " A.carrera = $carrera";
		}

		$sql = "SELECT  I.anio_academico as anio_ingreso, 
		                C.nombre_reducido as carrera, 	
		                A.legajo, 
                        P.apellido || ', ' || P.nombres as alumno, 
                        DECODE (P.sexo, 1, 'Masc', 2, 'Fem') AS sexo, 
                        DECODE (A.calidad, 'A', 'Activo', 'E', 'Egresado', 'P', 'Pasivo', 'N', 'Abandon�') AS calidad,
                        S.nombre AS sede
                FROM sga_alumnos A 
                JOIN sga_personas P on (P.nro_inscripcion = A.nro_inscripcion)
                JOIN sga_carrera_aspira ASP ON (A.unidad_academica = ASP.unidad_academica AND A.nro_inscripcion = ASP.nro_inscripcion AND A.carrera = ASP.carrera)
                JOIN sga_periodo_insc I ON (ASP.periodo_inscripcio = I.periodo_inscripcio)
                JOIN sga_carreras C ON (C.carrera = A.carrera)
                JOIN sga_sedes S ON (S.sede = A.sede)
                WHERE A.carrera not in (211, 290) 
                    AND A.legajo NOT IN (SELECT legajo FROM rep_alumnos_cohorte WHERE carrera = A.carrera)  ";
		
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$sql .= " ORDER BY I.anio_academico, C.nombre_reducido, 4";

		$db = MisConsultas::getConexion ();
		$alumnos_NO_cohorte = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $alumnos_NO_cohorte;
	}
	
	//Retorna los alumnos de cohorte que ingresaron un determinado a�o en una determinada carrera, y se reinscribieron otro determinado a�o
	function get_alumnos_cohorte_reinscriptos($filtro=array())
	{
		$where = array();
		if (isset($filtro['ANIO_ACADEMICO']) && isset($filtro['NOMBRE_CARRERA']) && isset($filtro['ANIO_REINSCRIPCION'])) 
		{	$anio = $filtro['ANIO_ACADEMICO'];
			$where[] = " A.anio_ingreso = $anio";
			$carrera = $filtro['NOMBRE_CARRERA'];
			$where[] = " A.carrera = $carrera";
			$anio_reinscripcion = $filtro['ANIO_REINSCRIPCION'];
			$where[] = " R.anio_academico = $anio_reinscripcion";
		}
		else
			return null;

		$sql = "SELECT  A.legajo, A.alumno, S.nombre AS sede, 
                                A.anio_ingreso, R.anio_academico AS anio_reinscripcion
                        FROM rep_alumnos_cohorte A
                        JOIN sga_reinscripcion R ON (A.carrera = R.carrera AND A.legajo = R.legajo)
                        JOIN sga_alumnos ALU ON (ALU.legajo = A.legajo AND ALU.carrera = A.carrera)
                        JOIN sga_sedes S ON (S.sede = ALU.sede)
                        WHERE A.carrera NOT IN (211,290) ";
		
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		
		$sql .= " ORDER BY anio_ingreso, alumno";

		$db = MisConsultas::getConexion ();
		$alumnos_cohorte = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $alumnos_cohorte;
	}

	//Retorna los alumnos que NO son cohorte, que ingresaron un determinado a�o en una determinada carrera, y se reinscribieron otro determinado a�o
	function get_alumnos_no_cohorte_reinscriptos($filtro=array())
	{
	        if (isset($filtro['ANIO_ACADEMICO']) && isset($filtro['NOMBRE_CARRERA']) && isset($filtro['ANIO_REINSCRIPCION'])) 
		{
                        $sql = "SELECT  A.legajo, 
                                        P.apellido || ', ' || P.nombres as alumno, 
                                        S.nombre AS sede,
                                        I.anio_academico AS anio_ingreso, 
                                        R.anio_academico AS anio_reinscripcion
                                FROM sga_alumnos A 
                                JOIN sga_personas P ON (P.nro_inscripcion = A.nro_inscripcion)
                                JOIN sga_carrera_aspira ASP ON (A.unidad_academica = ASP.unidad_academica AND A.nro_inscripcion = ASP.nro_inscripcion AND A.carrera = ASP.carrera)
                                JOIN sga_periodo_insc I ON (ASP.periodo_inscripcio = I.periodo_inscripcio)
                                JOIN sga_reinscripcion R ON (R.legajo = A.legajo and R.carrera = A.carrera)
                                JOIN sga_sedes S ON (S.sede = A.sede)
                                WHERE A.carrera <> 290	--Cursos Extracurriculares
                                        and A.legajo not in (select legajo from rep_alumnos_cohorte where carrera = A.carrera)
                                        AND I.anio_academico = {$filtro['ANIO_ACADEMICO']}
                                        and R.anio_academico = {$filtro['ANIO_REINSCRIPCION']}
                                        and A.carrera = {$filtro['NOMBRE_CARRERA']}
                                ORDER BY 3,2";
                }
                
		$db = MisConsultas::getConexion ();
		$alumnos_NO_cohorte = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $alumnos_NO_cohorte;
	}


        /*
     * Recupera datos del mini guarani, con el detalle de los inscirptos al curso de ingreso
     * Datos personales, y resultado del examen en cada fecha
     * Reporte v�lido hasta el a�o 2017
     */    
    static function get_datos_inscriptos_ingreso($anio) 
	{
            $db = MisConsultas::getConexionMini($anio);
            $sqlText = "EXECUTE PROCEDURE sp_getDatosInscriptos($anio)";
            $db->query($sqlText);

            $sql = "SELECT * FROM rep_datos_inscriptos WHERE periodo_inscripcio = '$anio'";
            $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			
            foreach($alumnos AS $alumno)
            {
                $carreras = consultas_extension::getCarreras($db, $alumno['NRO_INSCRIPCION']);
                $carrera1 = $carreras[0][1];				
                if (count($carreras) > 1)
                        $carrera2 = $carreras[1][1];
                else 
                        $carrera2 = '';

                $nro_inscripcion = $alumno['NRO_INSCRIPCION'];
                $sql = "SELECT fecha FROM vw_hist_academica V, sga_alumnos A
                            WHERE V.forma_aprobacion = 'Equivalencia'
                                    AND V.legajo = A.legajo
                                    AND V.carrera = A.carrera 
                                    AND A.nro_inscripcion = '$nro_inscripcion'";
                $equiv = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                $equivalencia = '-';
                $turno1 = '-';
                $turno2 = '-';
                $turno3 = '-';
                $turno4 = '-';
                $turno5 = '-';


                if (isset($equiv) and $equiv <> NULL)
                {
                    $equivalencia = $equiv[0]['FECHA'];
                }

                if ($equivalencia == '-')
                {
                    $sql = "SELECT E.turno_examen, V.resultado 
                                            FROM vw_hist_academica V, sga_actas_examen E, sga_alumnos A
                                            WHERE V.unidad_academica = E.unidad_academica
                                                    AND V.tipo_acta = E.tipo_acta
                                                    AND V.acta = E.acta
                                                    AND V.forma_aprobacion = 'Examen'
                                                    AND V.legajo = A.legajo	
                                                    AND V.carrera = A.carrera	
                                                    AND A.nro_inscripcion = '$nro_inscripcion'";
                    $examenes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($examenes AS $examen)
                    {
                        switch ($examen['TURNO_EXAMEN'])
                        {
                            case 1: 
                                    $turno1 = $examen['RESULTADO'];
                                    break;
                            case 2: 
                                    $turno2 = $examen['RESULTADO'];
                                    break;
                            case 3:
                                    $turno3 = $examen['RESULTADO'];
                                    break;
                            case 4:
                                    $turno4 = $examen['RESULTADO'];
                                    break;
                            case 5: 
                                    $turno5 = $examen['RESULTADO'];
                                    break;
                        }
                    }
                }

				
				
                $linea = array("nro_inscripcion" => $alumno['NRO_INSCRIPCION'], 
                                "inscripto" => $alumno['INSCRIPTO'], 
                                "nro_doc" => $alumno['NRO_DOC'],
                                "sexo" => ($alumno['SEXO'] == '1' ? 'M' : 'F'),
                                "fecha_nacim" => $alumno['FECHA_NACIM'],
                                "edad" => $alumno['EDAD'],
                                "equivalencia" => $equivalencia,
                                "turno1" => $turno1,
                                "turno2" => $turno2,
                                "turno3" => $turno3,
                                "turno4" => $turno4,
                                "turno5" => $turno5,
                                "carrera1" => $carrera1,
                                "carrera2" => $carrera2,
                                "colegio" => $alumno['COLEGIO'],
                                "loc_colegio" => $alumno['LOC_COLEGIO'],
                                "e_mail" => $alumno['E_MAIL'],
                                "celular" => $alumno['CELULAR'],
                                "loc_proced" => $alumno['LOC_PROCED'],
                                "direc_proced" => $alumno['DIREC_PROCED'],
                                "obra_social" => $alumno['OBRA_SOCIAL'],
                                "situacion_laboral" => $alumno['SITUACION_LABORAL'],
                                "remuneracion" => $alumno['REMUNERACION'],
                                "hora_sem_trab_alum" => $alumno['HORA_SEM_TRAB_ALUM'],
                                "rel_trab_carrera" => $alumno['REL_TRAB_CARRERA'],
                                "cant_fami_cargo" => $alumno['CANT_FAMI_CARGO'],
                                "existe_trab_alum" => $alumno['EXISTE_TRAB_ALUM'],
                                "cant_hijos_alum" => $alumno['CANT_HIJOS_ALUM'],
                                "vive_actual_con" => $alumno['VIVE_ACTUAL_CON'],
                                "tiene_beca" => $alumno['TIENE_BECA'],
                                "practica_deportes" => $alumno['PRACTICA_DEPORTES'],
                                "costea_est_con_aporte_fliares" => $alumno['COSTEA_EST_CON_APORTE_FLIARES'],
                                "costea_est_con_beca" => $alumno['COSTEA_EST_CON_BECA'],
                                "costea_est_con_plan_social" => $alumno['COSTEA_EST_CON_PLAN_SOCIAL'],
                                "costea_est_con_su_trabajo" => $alumno['COSTEA_EST_CON_SU_TRABAJO'],
                                "costea_est_con_otra_fuente" => $alumno['COSTEA_EST_CON_OTRA_FUENTE'],
                                "sit_laboral_padre" => $alumno['SIT_LABORAL_PADRE'],
                                "ult_est_cur_padre" => $alumno['ULT_EST_CUR_PADRE'],
                                "sit_laboral_madre" => $alumno['SIT_LABORAL_MADRE'],
                                "ult_est_cur_madre" => $alumno['ULT_EST_CUR_MADRE'],
                                "idioma_ingles" => $alumno['IDIOMA_INGLES'],
                                "como_te_enteraste" => $alumno['COMO_TE_ENTERASTE'],
                                "como_te_enteraste_pagina" => $alumno['COMO_TE_ENTERASTE_PAGINA'],
                                "como_te_enteraste_otros" => $alumno['COMO_TE_ENTERASTE_OTROS'],
                                "participaste_evento" => $alumno['PARTICIPASTE_EVENTO'],
                                "como_te_enteraste_cual" => $alumno['COMO_TE_ENTERASTE_CUAL'],
                                "como_te_enteraste_donde" => $alumno['COMO_TE_ENTERASTE_DONDE'],
                                "como_te_enteraste_cuando" => $alumno['COMO_TE_ENTERASTE_CUANDO'],
                                "como_te_enteraste_otro_motivo" => $alumno['COMO_TE_ENTERASTE_OTRO_MOTIVO'],
                                "discapacidad_leer" => $alumno['DISCAPACIDAD_LEER'],
                                "discapacidad_oir" => $alumno['DISCAPACIDAD_OIR'],
                                "discapacidad_caminar" => $alumno['DISCAPACIDAD_CAMINAR'],
                                "discapacidad_agarrar" => $alumno['DISCAPACIDAD_AGARRAR'],
                                "discapacidad_especificar" => $alumno['DISCAPACIDAD_ESPECIFICAR']);

                $data[] = $linea;
            }
            return $data;
	}

    /*
     * Recupera datos del mini guarani, con el detalle de los inscirptos al curso de ingreso
     * Datos personales, y resultado del examen en cada fecha
     * Reporte v�lido hasta el a�o 2017
     */  
    static function get_datos_inscriptos_ingreso_2018($anio) 
	{
            $db = MisConsultas::getConexionMini($anio);
//            $sqlText = "EXECUTE PROCEDURE sp_getDatosInscriptos($anio)";
//            $db->query($sqlText);

            $sql = "SELECT DISTINCT * FROM rep_datos_inscriptos WHERE periodo_inscripcio = '$anio'";
            $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			
            foreach($alumnos AS $alumno)
            {
                $carreras = consultas_extension::getCarreras($db, $alumno['NRO_INSCRIPCION']);
                $carrera1 = $carreras[0][1];				
                if (count($carreras) > 1)
                        $carrera2 = $carreras[1][1];
                else 
                        $carrera2 = '';

                $nro_inscripcion = $alumno['NRO_INSCRIPCION'];

                $sql = "SELECT DISTINCT fecha, materia 
                                FROM vw_hist_academica V, sga_alumnos A
                            WHERE lower(V.forma_aprobacion) = 'equivalencia'
                                    AND V.legajo = A.legajo
                                    AND V.carrera = A.carrera 
                                    AND resultado = 'A'
                                    AND A.nro_inscripcion = '$nro_inscripcion'";
                $equiv = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                $equivalencia = '-';
                $matematica = '-';
                $ivu = '-';
                $resolucion = '-';

                if (isset($equiv) and $equiv <> NULL)
                {
                    $equivalencia = 'Si: ';
                    foreach ($equiv AS $eq)
                    {
                        switch ($eq['MATERIA'])
                        {
                            case '001': 
                                    $matematica = $eq['FECHA'];
                                    $equivalencia .= 'Mat ';
                                    break;
                            case '002': 
                                    $ivu = $eq['FECHA'];
                                    $equivalencia .= 'Ivu ';
                                    break;
                            case '003':
                                    $resolucion = $eq['FECHA'];
                                    $equivalencia .= 'Res ';
                                    break;
                        }
                    }
                }

                $sql = "SELECT DISTINCT fecha, materia
                        FROM vw_hist_academica V, sga_alumnos A
                        WHERE lower(V.forma_aprobacion) NOT LIKE '%equiv%'
                                AND V.legajo = A.legajo
                                AND V.carrera = A.carrera 
                                AND A.nro_inscripcion = '$nro_inscripcion'
                                AND V.resultado = 'A'";
                $examenes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                foreach ($examenes AS $examen)
                {
                        switch ($examen['MATERIA'])
                        {
                                case '001': 
                                        $matematica = $examen['FECHA'];
                                        break;
                                case '002': 
                                        $ivu = $examen['FECHA'];
                                        break;
                                case '003':
                                        $resolucion = $examen['FECHA'];
                                        break;
                        }
                }
				
                $linea = array("sede" => $alumno['SEDE'], 
                                "nro_inscripcion" => $alumno['NRO_INSCRIPCION'], 
                                "inscripto" => $alumno['INSCRIPTO'], 
                                "nro_doc" => $alumno['NRO_DOC'],
                                "sexo" => ($alumno['SEXO'] == '1' ? 'M' : 'F'),
                                "fecha_nacim" => $alumno['FECHA_NACIM'],
                                "edad" => $alumno['EDAD'],
                                "equivalencia" => $equivalencia,
                                "matematica" => $matematica,
                                "ivu" => $ivu,
                                "resolucion" => $resolucion,
                                "carrera1" => $carrera1,
                                "carrera2" => $carrera2,
                                "colegio" => $alumno['COLEGIO'],
                                "loc_colegio" => $alumno['LOC_COLEGIO'],
                                "e_mail" => $alumno['E_MAIL'],
                                "celular" => $alumno['CELULAR'],
                                "loc_proced" => $alumno['LOC_PROCED'],
                                "direc_proced" => $alumno['DIREC_PROCED'],
                                "obra_social" => $alumno['OBRA_SOCIAL'],
                                "situacion_laboral" => $alumno['SITUACION_LABORAL'],
                                "remuneracion" => $alumno['REMUNERACION'],
                                "hora_sem_trab_alum" => $alumno['HORA_SEM_TRAB_ALUM'],
                                "rel_trab_carrera" => $alumno['REL_TRAB_CARRERA'],
                                "cant_fami_cargo" => $alumno['CANT_FAMI_CARGO'],
                                "existe_trab_alum" => $alumno['EXISTE_TRAB_ALUM'],
                                "cant_hijos_alum" => $alumno['CANT_HIJOS_ALUM'],
                                "vive_actual_con" => $alumno['VIVE_ACTUAL_CON'],
                                "tiene_beca" => $alumno['TIENE_BECA'],
                                "practica_deportes" => $alumno['PRACTICA_DEPORTES'],
                                "costea_est_con_aporte_fliares" => $alumno['COSTEA_EST_CON_APORTE_FLIARES'],
                                "costea_est_con_beca" => $alumno['COSTEA_EST_CON_BECA'],
                                "costea_est_con_plan_social" => $alumno['COSTEA_EST_CON_PLAN_SOCIAL'],
                                "costea_est_con_su_trabajo" => $alumno['COSTEA_EST_CON_SU_TRABAJO'],
                                "costea_est_con_otra_fuente" => $alumno['COSTEA_EST_CON_OTRA_FUENTE'],
                                "sit_laboral_padre" => $alumno['SIT_LABORAL_PADRE'],
                                "ult_est_cur_padre" => $alumno['ULT_EST_CUR_PADRE'],
                                "sit_laboral_madre" => $alumno['SIT_LABORAL_MADRE'],
                                "ult_est_cur_madre" => $alumno['ULT_EST_CUR_MADRE'],
                                "idioma_ingles" => $alumno['IDIOMA_INGLES'],
                                "como_te_enteraste" => $alumno['COMO_TE_ENTERASTE'],
                                "como_te_enteraste_pagina" => $alumno['COMO_TE_ENTERASTE_PAGINA'],
                                "como_te_enteraste_otros" => $alumno['COMO_TE_ENTERASTE_OTROS'],
                                "participaste_evento" => $alumno['PARTICIPASTE_EVENTO'],
                                "como_te_enteraste_cual" => $alumno['COMO_TE_ENTERASTE_CUAL'],
                                "como_te_enteraste_donde" => $alumno['COMO_TE_ENTERASTE_DONDE'],
                                "como_te_enteraste_cuando" => $alumno['COMO_TE_ENTERASTE_CUANDO'],
                                "como_te_enteraste_otro_motivo" => $alumno['COMO_TE_ENTERASTE_OTRO_MOTIVO'],
                                "discapacidad_leer" => $alumno['DISCAPACIDAD_LEER'],
                                "discapacidad_oir" => $alumno['DISCAPACIDAD_OIR'],
                                "discapacidad_caminar" => $alumno['DISCAPACIDAD_CAMINAR'],
                                "discapacidad_agarrar" => $alumno['DISCAPACIDAD_AGARRAR'],
                                "discapacidad_especificar" => $alumno['DISCAPACIDAD_ESPECIFICAR']);

                $data[] = $linea;
            }
            return $data;
	}
	
	static function get_datos_inscriptos_online($anio) 
	{
		$db = MisConsultas::getConexionMini($anio);

		$sql = "SELECT 	P.nro_documento, 
					P.apellido || ', ' || P.nombres AS inscripto, 
					VD.e_mail,
					L1.nombre AS localidad_procedencia,
					VD.calle_proc || ' ' || VD.numero_proc AS direc_procedencia,
					C.nombre AS colegio_secundario,
					L2.nombre AS localidad_colegio,
					CASE 	WHEN V.resultado = 'A' THEN 'Aprob�'
						WHEN V.resultado = 'R' THEN 'Reprob�'
						ELSE 'Ausente'
					END AS resultado_curso_online
				FROM sga_turnos_examen T, sga_insc_examen I
					LEFT JOIN sga_llamados_mesa M ON (M.materia = I.materia AND M.anio_academico = I.anio_academico AND M.turno_examen = I.turno_examen AND M.mesa_examen = I.mesa_examen AND M.llamado = I.llamado)
					LEFT JOIN sga_prestamos R ON (R.prestamo = M.prestamo)
					LEFT JOIN vw_hist_academica V ON (I.carrera = V.carrera AND I.legajo = V.legajo AND I.materia = V.materia AND V.fecha = R.fecha)
					JOIN sga_alumnos A ON (A.unidad_academica = I.unidad_academica AND A.carrera = I.carrera AND A.legajo = I.legajo)
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					JOIN vw_datos_censales_actuales VD ON (VD.unidad_academica = P.unidad_academica AND VD.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN mug_localidades L1 ON (VD.loc_proc = L1.localidad)
					LEFT JOIN sga_coleg_sec C ON (P.colegio_secundario = C.colegio)
					LEFT JOIN mug_localidades L2 ON (C.localidad = L2.localidad)
					WHERE T.anio_academico = $anio AND 
						T.nombre = 'Noviembre' AND
						T.anio_academico = I.anio_academico AND 
						T.turno_examen = I.turno_examen";
		$inscriptos_online = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		
		return $inscriptos_online;
	}
        
	//Retrona el c�digo y el nombre reducido de la/las carrera/s en que se encuentra inscripto un alumno dado.
	static function getCarreras($db, $nro_inscripcion) 
	{
		$sqlText = "SELECT A.carrera, B.nombre_reducido
						FROM sga_alumnos A, sga_carreras B 
								WHERE A.nro_inscripcion = '$nro_inscripcion'
									AND A.carrera = B.carrera
									AND A.carrera <> 290";
		$carreras = $db->query($sqlText)->fetchAll();
		return($carreras);
	}
	
	//Retorna la calidad del alumno en Guarani producci�n (Activo - Pasivo - Abandon� - Egresado)
	function getCalidad($dni, $carrera)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT A.calidad 
					FROM sga_alumnos A, sga_personas P
						WHERE A.unidad_academica = P.unidad_academica
						AND A.nro_inscripcion = P.nro_inscripcion
						AND P.nro_documento = '$dni'
						AND A.carrera = $carrera";
		$calidad = $db->query($sql)->fetchAll();
		return($calidad);				
	}
	
	//Retorna un arreglo con 2 valores: 
	//[0] -> Aprobado o Reprobado (o '' en caso que no se haya presentado en ninguna fecha). Aprobado, se especifica si es por Examen o por Equivalencia
	//[1] -> Fecha en el caso de haber aprobado (sino '')
	function getRindioExamen($db, $nro_inscripcion) {
		$sqlText = "SELECT DISTINCT resultado, fecha, forma_aprobacion FROM vw_hist_academica 
					WHERE legajo IN 
						(SELECT DISTINCT legajo FROM sga_alumnos 
						WHERE nro_inscripcion = '$nro_inscripcion')";
		$rindioExamen = $db->query($sqlText)->fetchAll();
		//print_r($rindioExamen);
		//ei_arbol($rindioExamen);
		$retorno[0] = '';
		$retorno[1] = '';
		$max = count($rindioExamen);
		if ($max == 1) 
		{
			if ($rindioExamen[0][0] == 'R') 
				$retorno[0] = 'Repr';
		}
		if ($max > 1) 
			$retorno[0] = 'Repr';
		for ($i = 0; $i < $max; $i++) {
			if ($rindioExamen[$i][0] == 'A') {
				$retorno[0] = 'Apr'.' - '.substr($rindioExamen[$i][2],0,2);
				$retorno[1] = $rindioExamen[$i][1];
			}
		}
		return($retorno);
	}
	
    //Retrona los alumnos que cumplen con los requisitos para cursar las PPS
    function get_alumnos_con_requisitos_para_cursar_PPS() 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "EXECUTE PROCEDURE sp_rep_pps()";
        $db->query($sqlText)->fetchAll();

        $sqlText = "SELECT S.legajo, P.apellido || ', ' || P.nombres AS alumno, D.e_mail, S.cant_mat
                        FROM rep_alu_sin_pps_206 S, sga_personas P, vw_datos_censales_actuales D
                        WHERE S.legajo = P.nro_inscripcion
                                AND P.unidad_academica = D.unidad_academica AND P.nro_inscripcion = D.nro_inscripcion
                                AND S.cant_mat >= 26
                        ORDER BY cant_mat DESC, 2";
        $conRequisitos = $db->query($sqlText)->fetchAll();	
        return $conRequisitos;
    }
	
    //Retrona los alumnos que NO cumplen con los requisitos para cursar las PPS
    function get_alumnos_sin_requisitos_para_cursar_PPS() 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "EXECUTE PROCEDURE sp_rep_pps()";
        $db->query($sqlText)->fetchAll();

        $sqlText = "SELECT S.legajo, P.apellido || ', ' || P.nombres AS alumno, D.e_mail, S.cant_mat
                        FROM rep_alu_sin_pps_206 S, sga_personas P, vw_datos_censales_actuales D
                        WHERE S.legajo = P.nro_inscripcion
                                AND P.unidad_academica = D.unidad_academica AND P.nro_inscripcion = D.nro_inscripcion
                                AND S.cant_mat < 26
                        ORDER BY cant_mat DESC, 2";
        $conRequisitos = $db->query($sqlText)->fetchAll();	
        return $conRequisitos;
    }

}

?>
