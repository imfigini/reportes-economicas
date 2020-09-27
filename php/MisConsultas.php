<?php
require_once('parametros.php');

class MisConsultas {
		
    static function get_parametros()
    {
        try {

            if (DESARROLLO) 
            {
                $host = '10.1.1.71';
                $server = 'ol_guarani2';
		        $clave = 'informix761';
            }
            else 
            {
                $host = '10.1.1.69';
                $server = 'ol_guarani';
				$clave = '2LeCh1IH';
            }

            $usuario = 'informix';
            $parametros = array('host'=>$host, 'server'=>$server, 'usuario'=>$usuario, 'clave'=>$clave);
			
            return $parametros;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            die();
        }
    }

    static function getConexion () 
    {
        try 
        {
            $parametros = MisConsultas::get_parametros();
            $host = $parametros['host'];
            $server = $parametros['server'];
            $usr = $parametros['usuario'];
            $clave = $parametros['clave'];
            $database = "siu_guarani";
            $strConexion = "informix:host=$host;service=1600;database=$database;server=$server;protocol=olsoctcp;EnableScrollableCursors=1";
            $conexion = new PDO($strConexion, "$usr", "$clave");
            return $conexion;
        }
        catch(PDOException $e)
        {
            toba::notificacion()->agregar($e->getMessage());
            die();
        }
    }

    static function getConexionPostgrado () 
    {
        try 
        {
            $parametros = MisConsultas::get_parametros();
            if (DESARROLLO)
            {
                $host = $parametros['host'];
                $server = $parametros['server'];
                $usr = $parametros['usuario'];
                $clave = $parametros['clave'];
                $database = "exap_guarani";
                $service = 1600;
            }
            else {
                $host = 'rproxy.exa.unicen.edu.ar';
                $server = 'informix';
                $usr = 'informix';
                $clave = 'in4mix';
                $database = "exap_guarani";
                $service = 9098;
            }
            $strConexion = "informix:host=$host;service=$service;database=$database;server=$server;protocol=olsoctcp;EnableScrollableCursors=1";
            $conexion = new PDO($strConexion, "$usr", "$clave");

//			$strConexion = "informix:host=10.1.1.71;service=1600;database=exap_guarani;server=ol_guarani2;protocol=olsoctcp;EnableScrollableCursors=1";
//			$conexion = new PDO($strConexion, "informix", "informix761");
            return $conexion;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            die();
        }
    }
		
    static function getConexionMini ($anio = NULL) 
    {
        try 
        {
            if (is_null($anio))
            {
                $anio = 2018;
            }
            $parametros = self::get_parametros();
            $host = $parametros['host'];
            $server = $parametros['server'];
            $usr = $parametros['usuario'];
            $clave = $parametros['clave'];
            if ($anio < 2018)
            {
                $database = 'ingr_guarani_'.$anio;
            }
            else
            {
                $database = 'ingr_guarani';
            }
            $strConexion = "informix:host=$host;service=1600;database=$database;server=$server;protocol=olsoctcp;EnableScrollableCursors=1";
            $conexion = new PDO($strConexion, "$usr", "$clave");
            return $conexion;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            die();
        }
    }

    function getServer()
    {
        if (DESARROLLO) 
        {
            return 'ol_guarani2';
        }
        else 
        {
            return 'ol_guarani';
        }
    }
    
    static function query($sqlText) 
    {
        $db = MisConsultas::getConexion ();
        //echo($sqlText.PHP_EOL);
        $datos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);

        $datos = MisConsultas::addFakeId($datos);

        return $datos;			
    }
	
    static function queryPosgrado($sqlText) 
    {
        $db = MisConsultas::getConexionPostgrado();
								 
																  

		$resultado = $db->query($sqlText);
		
        $datos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        $datos = MisConsultas::addFakeId($datos);

        return $datos;			
    }	
		
    
    static function getPais() 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT pais, nombre, 0
                            FROM mug_paises
                            WHERE nombre LIKE 'Argentina'
                    UNION	
                    SELECT pais, nombre, pais
                            FROM mug_paises
                            WHERE nombre NOT LIKE 'Argentina'
                            ORDER BY 3";

        $anios = $db->query($sqlText);

        return $anios;
    }
    
    static function getNombreMateria($materia)
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT nombre
                            FROM sga_materias
                            WHERE materia = '$materia'";

        $materia = $db->query($sqlText)->fetchall();

        return $materia['NOMBRE'];
    }
    
    

    static function getProvincia($pais) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT provincia, nombre, 0
                            FROM mug_provincias
                            WHERE nombre LIKE 'Buenos Aires' AND pais = $pais
                    UNION	
                    SELECT provincia, nombre, provincia
                            FROM mug_provincias
                            WHERE nombre NOT LIKE 'Buenos Aires' AND pais = $pais
                            ORDER BY 3";

        $anios = $db->query($sqlText);

        return $anios;
    }		
		
    static function getPartido($provincia) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT dpto_partido, nombre, 0
                            FROM mug_dptos_partidos
                            WHERE nombre LIKE 'Tandil' AND provincia = $provincia
                    UNION	
                    SELECT dpto_partido, nombre, dpto_partido
                            FROM mug_dptos_partidos
                            WHERE nombre NOT LIKE 'Tandil' AND provincia = $provincia
                            ORDER BY 3";

        $anios = $db->query($sqlText);

        return $anios;
    }

    static function getLocalidad($dpto_partido) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT localidad, nombre, 0
                            FROM mug_localidades
                            WHERE nombre LIKE 'TANDIL' AND dpto_partido = $dpto_partido
                    UNION	
                    SELECT localidad, nombre, dpto_partido
                            FROM mug_localidades
                            WHERE nombre NOT LIKE 'TANDIL' AND dpto_partido = $dpto_partido
                            ORDER BY 3";

        $anios = $db->query($sqlText);

        return $anios;
    }
		
    static function get_localidades($filtro=array())
    {
        $db = MisConsultas::getConexion ();

        $where = array();

        if (isset($filtro['pais'])) {
                $where[] = "P.pais = ".$filtro['pais'];
        }			

        if (isset($filtro['provincia'])) {
                $where[] = "M.provincia = ".$filtro['provincia'];
        }			

        if (isset($filtro['partido'])) {
                $where[] = "D.dpto_partido = ".$filtro['partido'];
        }			

        if (isset($filtro['localidad'])) {
                $where[] = "L.localidad = ".$filtro['localidad'];
        }			

        $sqlText = "SELECT L.localidad
                        FROM mug_localidades L, mug_dptos_partidos D, mug_provincias M, mug_paises P
                        WHERE 
                                L.dpto_partido = D.dpto_partido AND
                                D.provincia = M.provincia AND
                                M.pais = P.pais";

        if (count($where) > 0) 
        {
            $sqlText = $sqlText .' AND '. implode(' AND ', $where);
        }

        $localidades = $db->query($sqlText)->fetchAll(PDO::FETCH_NUM); //PDO::FETCH_ASSOC);
        $resultado = array();
        foreach ($localidades as $localidad)
        {
                $resultado[] = $localidad[0];
        }

        return $resultado;		
    }

    static function addPorcentaje($db, $filas)
    {
        $resultado = array();

        foreach ($filas as $fila)
        {
            $unidad_academica 	= $fila['UNIDAD_ACADEMICA'];
            $legajo 		= $fila['LEGAJO'];
            $carrera 		= $fila['CARRERA'];

            $sql = "EXECUTE PROCEDURE sp_porc_exa ('$unidad_academica', '$carrera', '$legajo')";

            $porcentaje = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $fila['PORCENTAJE'] = implode($porcentaje['0']);

            $resultado[] = $fila;
        }

        return $resultado;
    }
		
		
    static function getAlumnosLocalidad($filtro=array())
    {
        $db = MisConsultas::getConexion ();

        $localidades = MisConsultas::get_localidades($filtro);
        $localidades = implode(',' , $localidades);
        // Con el array de localidades puedo construir una cadena (loc1, loc2, ... , locn) para usar con un IN de SQL
        $sqlText = "SELECT sga_personas.apellido || ', ' || sga_personas.nombres AS nombre,
                            sga_carreras.nombre AS carrera_nombre,
                            vw_datos_censales_actuales.e_mail,
                            vw_datos_cen_aux_actuales.celular_numero, sga_coleg_sec.nombre AS colegio_secundario,
                            sga_alumnos.unidad_academica,
                            sga_alumnos.carrera, 
                            sga_alumnos.legajo
                    FROM sga_personas,
                            sga_carrera_aspira,
                            sga_alumnos,
                            sga_carreras,
                            vw_datos_censales_actuales,
                            vw_datos_cen_aux_actuales,
                            OUTER sga_coleg_sec
                    WHERE 
                            sga_alumnos.regular = 'S' AND
                            sga_alumnos.calidad = 'A' AND
                            sga_personas.nro_inscripcion IN
                                    (SELECT nro_inscripcion
                                     FROM vw_datos_censales_actuales
                                     WHERE loc_proc IN ($localidades)
                                     UNION SELECT nro_inscripcion
                                     FROM sga_personas
                                     WHERE loc_nacimiento IN ($localidades)
                                     UNION SELECT nro_inscripcion
                                     FROM sga_personas
                                     WHERE colegio_secundario IN
                                             (SELECT colegio
                                              FROM sga_coleg_sec
                                              WHERE localidad IN ($localidades)))
                            AND vw_datos_censales_actuales.nro_inscripcion = sga_personas.nro_inscripcion
                            AND vw_datos_cen_aux_actuales.nro_inscripcion = sga_personas.nro_inscripcion
                            AND sga_personas.nro_inscripcion = sga_carrera_aspira.nro_inscripcion
                            AND sga_carrera_aspira.nro_inscripcion = sga_alumnos.nro_inscripcion
                            AND sga_carrera_aspira.carrera = sga_alumnos.carrera
                            AND sga_carreras.carrera = sga_alumnos.carrera
                            AND sga_coleg_sec.colegio = sga_personas.colegio_secundario";

        if (isset($filtro['carrera'])) 
        {
            $sqlText .= " AND sga_alumnos.carrera = ".$filtro['carrera'];
        }	

        $sqlText .= " ORDER BY 1";

        $alumnos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);

        $alumnos = MisConsultas::addPorcentaje($db, $alumnos);
        $alumnos = MisConsultas::addFakeId($alumnos);

        return $alumnos;
    }

    static function getAniosAcademicos ($otroAnio = null) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT anio_academico
                        FROM sga_anio_academico";

        if (isset($otroAnio))
        {
            $sqlText .= " WHERE anio_academico > $otroAnio";
        }

        $sqlText .= " ORDER BY 1 DESC";

        $anios = $db->query($sqlText);
        return $anios;
    }	
    
    static function getTurnosExamen ($anio_academico) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT turno_examen, nombre
                        FROM sga_turnos_examen
                        WHERE anio_academico = $anio_academico";

        $turnos = $db->query($sqlText);
        
        return $turnos;
    }	
     
    static function getLlamadosExamen ($anio_academico, $turno_examen) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT llamado, fecha_inicio || ':' || fecha_fin AS nombre
                        FROM sga_llamados
                        WHERE anio_academico = '$anio_academico'"
                . "     AND turno_examen = '$turno_examen'";

        $llamados = $db->query($sqlText);
        
        return $llamados;
    }  
    
    static function getMateriasLlamado($anio_academico, $turno_examen, $llamado = null)
    {
        $db = MisConsultas::getConexion ();
        
        $where = array();
        
        $where[] = "sga_llamados_mesa.anio_academico = '$anio_academico'";
        $where[] = "sga_llamados_mesa.turno_examen = '$turno_examen'";
        if (isset($llamado))
        {
            $where[] = "sga_llamados_mesa.llamado = '$llamado'";
        }
        
        $where = implode(" AND ", $where);

        $sqlText = "SELECT sga_materias.materia, '(' || sga_materias.materia || ') ' || sga_materias.nombre AS nombre, sga_materias.nombre AS solo_nombre
                    FROM sga_llamados_mesa, sga_materias
                    WHERE sga_llamados_mesa.materia = sga_materias.materia
                    AND $where "
                . "ORDER BY solo_nombre";

        $materias = $db->query($sqlText);
        
        return $materias;
    }

    static function get_anios_miniGuarani() 
    {
        $db = self::getConexionMini(2018);
        $sql = 'SELECT anio_academico FROM sga_anio_academico ORDER BY 1 DESC';
        return $db->query($sql);
    }
    
    static function getAniosAcademicos_miniGuarani () 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT anio_academico
                        FROM sga_anio_academico
                                WHERE anio_academico >= 2014
                        ORDER BY 1 DESC";

        $anios = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);

        //Los a�os acad�micos de mini guarani son uno m�s que en producci�n despu�s de agosto. Lo agrego al ppio.
        $hoy = new DateTime("now");
        $y = $hoy->format('Y');
        $fecha = new DateTime("$y-08-01");

        if ($hoy > $fecha)
        {
            array_unshift($anios, Array('ANIO_ACADEMICO' => $y + 1));
        }
        
        return $anios;
    }	

    static function getPeriodosLectivos ($anio_academico) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT periodo_lectivo
                        FROM sga_periodos_lect
                        WHERE anio_academico = $anio_academico
                        ORDER BY 1 DESC";

        $periodo_lectivo = $db->query($sqlText);
        return $periodo_lectivo;
    }

    static function getMateriasConComisionAnioPeriodo ($anio_academico, $periodo_lectivo) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT DISTINCT sga_materias.materia, 
                                '(' || sga_materias.materia || ') ' || sga_materias.nombre AS nombre, 
                                sga_materias.nombre AS solo_nombre
                        FROM sga_comisiones, sga_materias
                        WHERE 
                                sga_comisiones.materia = sga_materias.materia AND
                                sga_comisiones.anio_academico = $anio_academico AND sga_comisiones.periodo_lectivo = '$periodo_lectivo'
                    ORDER BY 3";

        $materias = $db->query($sqlText);
        return $materias;
    }
		
    static function getInscriptosMateriaAnioPeriodo($parametros)
    {
        $db = MisConsultas::getConexion ();

        $anio_academico = $parametros['anio_academico'];
        $periodo_lectivo = $parametros['periodo_lectivo'];
        $materia = $parametros['materia'];
        
        $sql = "SELECT P.apellido, P.nombres, P.nro_documento, G.e_mail, S.nombre as sede
                    FROM sga_insc_cursadas I
                    JOIN sga_alumnos A ON (A.legajo = I.legajo AND A.carrera = I.carrera)
                    JOIN sga_comisiones C ON (C.comision = I.comision)
                    JOIN sga_sedes S ON (S.sede = C.sede)
                    JOIN sga_personas P ON (A.nro_inscripcion = P.nro_inscripcion)
                    LEFT JOIN gda_anun_conf_pers G ON (P.nro_inscripcion = G.nro_inscripcion)
                        WHERE C.anio_academico = $anio_academico AND
                            C.periodo_lectivo = '$periodo_lectivo' AND
                            C.materia = '$materia' ";

        if (isset($parametros['sede']))
        {
            $sede = $parametros['sede'];
            $sql .= " AND S.sede = '$sede' ";
        }
        $datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return MisConsultas::addFakeId($datos);
    }
	
    //Recupera los docentes de planta (x Majen) que tienen sus funciones en una determinada materia. 
    static function getDocentesMateriaAnioPeriodo($parametros)
    {
        $anio_academico = $parametros['anio_academico'];
        $cuatrim = $parametros['periodo_lectivo'][0];
        $materia = $parametros['materia'];
        
        $sql = "SELECT DISTINCT D.legajo, 
                        V.apellido_nombres, 
                        D.documento,
                        CASE 	WHEN V.responsable = '1' THEN 'S'
                                WHEN V.responsable = '0' THEN 'N'
                                ELSE V.responsable 
                        END AS responsable,
                        COALESCE(categoria, '') AS categoria
                FROM vw_planta_funcional V
                JOIN docentes D ON (D.id = V.docente_id)
                    WHERE anio_academico_nombre = '$anio_academico'
                    AND tipo_cuatrimestre LIKE '$cuatrim%'
                    AND codigo_asignatura = '$materia' 
                ORDER BY V.apellido_nombres ";

        $datos = toba::db('Docentes')->consultar($sql);
        return self::get_mails_docentes($datos);			
    }
   
    //Recupera todos los docentes de planta responsables de alguna materia (x Majen).
    static function getDocentesResponsablesAnioPeriodo($parametros)
    {
        $anio_academico = $parametros['anio_academico'];
        $cuatrim = $parametros['periodo_lectivo'][0];
        
        $sql = "SELECT DISTINCT D.legajo, 
                        V.apellido_nombres, 
                        D.documento
                FROM vw_planta_funcional V 
                JOIN docentes D ON (D.id = V.docente_id) 
                    WHERE anio_academico_nombre = '$anio_academico'
                    AND tipo_cuatrimestre LIKE '$cuatrim%'
                    AND V.responsable IN ('1', 'S') 
                ORDER BY V.apellido_nombres ";

        $datos = toba::db('Docentes')->consultar($sql);
        return self::get_mails_docentes($datos);	
    }
    
    
    static function get_mails_docentes($datos)
    {
        $db = MisConsultas::getConexion ();
        $result = array();
        foreach ($datos AS $dato)
        {
            $legajo = $dato['legajo'];
            $sql = "SELECT e_mail
                    FROM gda_anun_conf_pers G
                    JOIN sga_docentes D ON (D.nro_inscripcion = G.nro_inscripcion)
                        WHERE D.legajo = '$legajo' ";
            $mail = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if (isset($mail[0]['E_MAIL'])) 
            {
                $dato['email'] = $mail[0]['E_MAIL'];
            }
            $result[] = $dato;            
        }
        return $result;
     }
    
    static function addFakeId($datos) 
    {
        $resultado = array();
        $i = 1;
        foreach ($datos as $dato) 
        {
            $dato['id'] = $i;
            $i++;
            $resultado[] = $dato;
        }
        return $resultado;
    }

    static function getInfoAlumnoCarrera($db, $alumno, $anio_academico) 
    {
        $sqlText = "SELECT C.nombre_reducido AS carrera, YEAR(A.fecha_ingreso)
                        FROM sga_alumnos A, sga_carreras C
                        WHERE A.legajo = '$alumno'
                            AND YEAR(A.fecha_ingreso) = '$anio_academico'
                            AND A.carrera = C.carrera";

        $carreras = $db->query($sqlText);
        if ($carreras != False) 
        {
            $carreras = $carreras->fetchAll(PDO::FETCH_ASSOC);
            return $carreras;
        }
        else
        {	
            echo "Se produjo un error en la linea ".__line__." del archivo ".__file__;
        }
    }
		
    static function getDatosNuervosInscriptos($filtro=array()) 
    {
        if (!array_key_exists('ANIO_ACADEMICO', $filtro)) 
        {
            return;
        } 

        $anio_academico = $filtro['ANIO_ACADEMICO'];

        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT DISTINCT S.nombre AS sede, 
                                    legajo, 
                                    (apellido || ', ' || nombres) AS nombres, 
                                    dni, 
                                    fecha_nacim,
                                    e_mail, 
                                    ciudad_proced, 
                                    prov_proced, 
                                    colegio_secundario, 
                                    ciudad_colegio, 
                                    prov_colegio
                        FROM rep_nuevos_inscriptos R
                        JOIN sga_sedes S ON (S.sede = R.sede)";

        if ($anio_academico != null) 
        {
            $sqlText .= "WHERE anio_ingreso = '$anio_academico' ";
        }

        $sqlText .= "ORDER BY nombres";

        $datos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);

        $datos = MisConsultas::addFakeId($datos);
        $alumnos = MisConsultas::agregaCarreras($datos, $anio_academico);

        return $alumnos;
    }	

    static function agregaCarreras($datos, $anio_academico) 
    {
        $resultado = array();
        $db = 	MisConsultas::getConexion ();	

        foreach ($datos as $dato) 
        {
            $legajo = $dato['LEGAJO'];
            $carreras = MisConsultas::getInfoAlumnoCarrera($db, $legajo, $anio_academico);
            $i = 1;
            foreach ($carreras as $carrera) 
            {
                $dato["CARRERA".$i] = $carrera['CARRERA'];
                $i++;
            }
            $resultado[] = $dato;
        }
        return $resultado;
    }		

    static function getCarreras () 
    {
        return self::get_carreras();
    }
    
    static function getCarrerasMateria($materia)
    {
        $db = MisConsultas::getConexion();
        $sqlText = "SELECT sga_carreras.carrera, sga_carreras.nombre
                    FROM sga_atrib_mat_plan, sga_carreras
                    WHERE sga_atrib_mat_plan.carrera = sga_carreras.carrera AND
                    sga_atrib_mat_plan.materia = '$materia'
                    ORDER BY 2
                    ";

        $carreras = $db->query($sqlText);
        return $carreras;        
    }

    static function getMaterias ($carrera) 
    {
        $db = MisConsultas::getConexion();

        $sqlText = "SELECT DISTINCT A.materia, A.nombre_materia || ' (' || A.materia || ')' AS nombre_materia
                        FROM sga_atrib_mat_plan A
                        JOIN sga_planes P ON (A.unidad_academica = P.unidad_academica AND A.carrera = P.carrera AND A.plan = P.plan AND A.version = P.version_actual)
                        WHERE A.carrera = $carrera
                        ORDER BY 2";

        $materias = $db->query($sqlText);
        return $materias;
    }	
                
    static function getCarrerasPosgrado () 
    {
        $db = MisConsultas::getConexionPostgrado ();

        $sqlText = "SELECT nombre, carrera 
                        FROM sga_carreras
                        ORDER BY 1 DESC";

        $anios = $db->query($sqlText);
        return $anios;
    }	
		
    static function getDatosMails($filtro=array()) 
    {
        if (!array_key_exists('CARRERA', $filtro)) 
        {
            return;
        } 

        $carrera = $filtro['CARRERA'];

        $db = MisConsultas::getConexionPostgrado ();

        $sqlText = "SELECT DISTINCT     A.legajo, 
                                        (B.apellido || ', ' || B.nombres) AS alumno, 
                                        B.nro_documento AS dni, 
                                        C.e_mail,
                                        DECODE(A.calidad,'A', 'Activo', 'P', 'Pasivo', 'E', 'Egresado', 'N', 'Abandon�', 'No informa') as calidad,
                                        A.fecha_ingreso
                                FROM sga_alumnos A, sga_personas B, vw_datos_censales_actuales C
                                WHERE 	A.nro_inscripcion = B.nro_inscripcion
                                        AND B.nro_inscripcion = C.nro_inscripcion 
                                        AND A.calidad = 'A'";

        if ($carrera != null) 
        {
            $sqlText .= "AND A.carrera = '$carrera' ";
        }

        $sqlText .= " ORDER BY 2";
       // print_r($sqlText);
        $datos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
        $datos = MisConsultas::addFakeId($datos);
        return $datos;
    }
		
    /** Retorna el listado carreras que tienen plan activo vigente **/
    static function get_carreras() 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT carrera, nombre || ' (' || carrera || ')' AS nombre 
                        FROM sga_carreras 
                        WHERE estado = 'A'
                                AND carrera <> 290
                        ORDER BY nombre;";

        $carreras = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
        return $carreras;
    }	
                
    /** Retorna el listado de materias de una determinada carrera pertenecientes al plan activo vigente **/
    static function get_materias($carrera) 
    {
        $sqlText = "SELECT DISTINCT M.materia, M.nombre || ' (' || M.materia || ')' AS nombre
                        FROM sga_materias M, sga_atrib_mat_plan A, sga_planes P
                        WHERE M.unidad_academica = A.unidad_academica AND M.materia = A.materia
                                AND A.unidad_academica = P.unidad_academica AND A.carrera = P.carrera AND A.plan = P.plan AND A.version = P.version_actual
                                AND P.estado = 'V'";    //Activo Vigente
        if (isset($carrera))
        {
            $sqlText .= " AND P.carrera = $carrera";
        }

        $sqlText .= " ORDER BY nombre";

        $db = MisConsultas::getConexion ();
        $materias = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
        return $materias;

    }	

    /** Retorna el listado de alumnos que tienen aprobada una determinada materia de una determinada carrera**/
    /** Verifica que el alumno no est� agresado en alguna carrera **/
    static function get_alumnos_aprobados($carrera, $materia)
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT DISTINCT A.legajo, P.apellido || ', ' || P.nombres AS alumno, V.fecha, V.nota, A.plan
                        FROM vw_hist_academica V
                        JOIN sga_alumnos A ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo)
                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                        WHERE V.resultado IN ('A', 'P')
                                AND V.materia = '$materia'
                                AND V.carrera = $carrera
                                AND A.calidad = 'A' AND A.regular = 'S'
                                AND A.legajo NOT IN (SELECT legajo FROM sga_alumnos WHERE calidad = 'E')
                        ORDER BY alumno";

        $alumnos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
        return $alumnos;			
    }
		
    // Recupera las carreras en las que figura con anterioridad el alumno
    static function getCarreraAnteriores($db, $legajo, $fecha)
    {
        $mdy = substr($fecha, 5, 2).','.substr($fecha, 8, 2).','.substr($fecha, 0, 4);

        $sql = "SELECT sga_carreras.carrera
                FROM sga_alumnos, sga_carreras
                    WHERE 
                        sga_alumnos.carrera = sga_carreras.carrera AND
                        sga_alumnos.legajo = '$legajo' AND 
                        sga_alumnos.fecha_ingreso < MDY($mdy)";

        $carreras = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        if (count($carreras) > 0)
        {
            return implode('|', $carreras);
        } 
        else
        {
            return "";
        }
    }

    // Recupera las materias de un plan/carrera/version para un determinado a�o de cursada
    static function getMateriasAnio($db, $carrera, $plan, $version, $anio_de_cursada)
    {
        $sql = "SELECT materia 
                    FROM sga_atrib_mat_plan 
                    WHERE 
                        carrera = '$carrera' AND
                        plan = '$plan' AND
                        version = '$version' AND
                        anio_de_cursada = '$anio_de_cursada'";

        $materias = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $materias;
    }
	
    // Recupera el plan y version de un alumno
    static function getPlanVersionAlumno($db, $carrera, $legajo, $fecha_ingreso)
    {
        $sql = "EXECUTE PROCEDURE sp_plan_de_alumno('EXA', '$carrera', '$legajo', '$fecha_ingreso 00:00:00')";
        $plan_version = $db->query($sql)->fetchAll();
        $plan_version = array('PLAN' => $plan_version[0][0], 'VERSION' => $plan_version[0][1]);

        return $plan_version;
    }
		
    // Devuelve las materias aprobadas por promocion o por examen y las cursadas activas
    static function getMateriasAprobadasAlumno($db, $carrera, $legajo, $fecha)
    {
        $mdy = substr($fecha, 5, 2).','.substr($fecha, 8, 2).','.substr($fecha, 0, 4);

        $sql = "SELECT materia 
                    FROM vw_hist_academica
                            WHERE carrera = '$carrera' AND legajo = '$legajo' AND fecha <= MDY($mdy) AND resultado = 'A'
                    UNION 
                    SELECT materia
                    FROM sga_cursadas
                            WHERE carrera = '$carrera' AND legajo = '$legajo' AND fecha_regularidad <= MDY($mdy) AND resultado IN ('A', 'P')";

        $aprobadas = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $aprobadas;
    }
		
		
    // Devuelve las materias de primero que le faltan.
    static function faltaMateriaPrimero($db, $legajo, $carrera, $fecha_ingreso)
    {
        $plan_version = self::getPlanVersionAlumno($db, $carrera, $legajo, $fecha_ingreso);
        $plan = $plan_version['PLAN'];
        $version = $plan_version['VERSION'];

        $materias_primero = self::getMateriasAnio($db, $carrera, $plan, $version, 1);
        $aprobadas = self::getMateriasAprobadasAlumno($db, $carrera, $legajo, $fecha_ingreso);

        $faltantes = array();
        foreach ($materias_primero as $materia)
        {
            if (!array_search($materia, $aprobadas))
            {
                    $faltantes[] = $materia;
            }
        }

        if (count($faltantes) == count($materias_primero)) 
        {
            $faltantes = array('TODAS');
        }
        // Si no encontro ninguna materia necesaria, que no esta aprobada.
        return $faltantes;
    }
		
		// Agrerega una columna con el origen de un alumno
		static public function addOrigen($db, $alumnos)
		{
			$resultado = array();
			
			foreach ($alumnos as $alumno)
			{
				$legajo = $alumno[4];
				$fecha_ingreso = $alumno[5];
				
				$carreras_anteriores = self::getCarreraAnteriores($db, $legajo, $fecha_ingreso);
				if ($carreras_anteriores != "")
				{
					$alumno['anteriores'] = $carreras_anteriores;
					$resultado[] = $alumno;
				}
			}
			
			return $resultado;
		}
		
    // Agrega en el listado solo aquellos alumnos que adeudan una materia de primero de la nueva carrera
    static public function filtrarConUnaMateria($db, $alumnos)
    {
        $resultado = array();

        foreach ($alumnos as $alumno)
        {
            $legajo = $alumno[4];
            $carrera = $alumno[3];
            $fecha_ingreso = $alumno[5];

            $faltantes = MisConsultas::faltaMateriaPrimero($db, $legajo, $carrera, $fecha_ingreso);
            if (count($faltantes) > 0)
            {
                $alumno['faltantes'] = implode('|', $faltantes);
                $resultado[] = $alumno;
            }
        }
        return $resultado;
    }
		
    static public function agregaDatosPersonales($db, $alumno)
    {
            $legajo = $alumno[4];
            $sql = "SELECT 
                            DECODE(sexo, 1, 'M', 2, 'F'), nro_documento, fecha_nacimiento, TRUNC((fecha_ingreso - fecha_nacimiento)/365),
                            sga_coleg_sec.nombre, mug_localidades.nombre AS loc_procedencia
                            FROM sga_alumnos,
                                    sga_personas,
                                    OUTER mug_localidades, sga_coleg_sec

                            WHERE 
                                    sga_alumnos.nro_inscripcion = sga_personas.nro_inscripcion AND
                                    sga_coleg_sec.colegio = sga_personas.colegio_secundario AND
                                    mug_localidades.localidad = sga_coleg_sec.localidad AND
                                    sga_personas.nro_inscripcion = '$legajo'";

            $datos = $db->query($sql)->fetchAll(PDO::FETCH_NUM);

            if (count($datos) >0)
            {
                    $alumno = array_merge($alumno, $datos[0]);
            }
            return $alumno;
    }

    static function filtrarSituacionLaboral($valor, $alumno)
    {
            switch ($valor)
            {
                    case 1: {return 'Trabajó al menos 1 h la última semama';}; break;
                    case 2: {return 'No trabajó y buscó';}; break;
                    case 3: {return 'No trabajó y no buscó';}; break;
            }
            return "";
    }

    static function filtrarSituacionLaboralPadre($valor, $alumno)
    {
            switch ($valor)
            {
                    case 1: {return 'Trabaj� al menos 1 h la �ltima semama';}; break;
                    case 2: {return 'No trabaj� y busc�';}; break;
                    case 3: {return 'No trabaj� y no busc�';}; break;
                    case 4: {return 'Desconoce';}; break;
            }
            return "";
    }

    static function filtrarHorasTrabajo($valor, $alumno)
    {
            switch ($valor)
            {
                    case 1: {return 'Hasta 10 hs';}; break;
                    case 2: {return 'Entre 10 y 20 hs';}; break;
                    case 3: {return 'Entre 20 y 35 hs';}; break;
                    case 4: {return 'Mas de 35 hs';}; break;
            }
            return "";
    }

    static function filtrarRelacionTrabajoEstudio($valor, $alumno)
    {
            switch ($valor)
            {
                    case 1: {return 'Total';}; break;
                    case 2: {return 'Parcial';}; break;
                    case 3: {return 'Sin Relaci�n';}; break;
            }
            return "";
    }

    static function filtrarUltimosEstudiosPadre($valor, $alumno)
    {
            switch ($valor)
            {
                    case 1: {return 'No hizo estudios';}; break;
    case 2: {return 'Estudios primarios incompletos';}; break;
    case 3: {return 'Estudios primarios completos';}; break;
    case 4: {return 'Estudios secundarios incompletos';}; break;
    case 5: {return 'Estudios secundarios completos';}; break;
    case 8: {return 'Estudios superiores incompletos';}; break;
    case 9: {return 'Estudios superiores completos';}; break;
    case 10: {return 'Estudios universitarios incompletos';}; break;
    case 11: {return 'Estudios universitarios completos';}; break;
    case 12: {return 'Estudios de Post grado';}; break;
                    case 13: {return 'Desconoce';}; break;
            }
            return "";
    }		

    static function filtrarActividadEconomicaPadre($valor, $alumno)
    {
            switch ($valor)
            {
                    case	1: {return 'Agricultura, Ganadeía y Minería';}; break;
                    case	2: {return 'Industria y Construcción';}; break;
                    case	3: {return 'Comercio';}; break;
                    case	4: {return 'Bancos, Bolsas, Seguros y Sociedades Financieras';}; break;
                    case	5: {return 'Enseñanza';}; break;
                    case	6: {return 'Entes Civiles del Estado';}; break;
                    case	7: {return 'Fuerzas Armadas y de Seguridad';}; break;
                    case	8: {return 'Ejercicio de profesión liberal';}; break;
                    case	9: {return 'Servicios públicos y privados part.';}; break;
                    case	10: {return 'Instituciones deportivas y afines';}; break;
                    case	11: {return 'Artes en general y actividades afines';}; break;
                    case	12: {return 'Medios de comunicación';}; break;
                    case	13: {return 'Ocupaciones varias';}; break;
                    case	14: {return 'Explotación de minas y canteras';}; break;
                    case	15: {return 'Hoteles y restaurantes';}; break;
                    case	16: {return 'Transporte, almacenamiento y comunicación';}; break;
                    case	17: {return 'Actividades inmobiliarias, empresariales y de alquiler';}; break;
                    case	18: {return 'Servicios sociales y de salud';}; break;
                    case	19: {return 'Otras actividades de servicios comunit., soc. y personales';}; break;
                    case	20: {return 'Hogares privados con servicio doméstico';}; break;
                    case	21: {return 'Organizaciones y organos extraterritoriales';}; break;
                    case	22: {return 'Industrias Manufactureras';}; break;
                    case	23: {return 'Suministro de Electricidad';}; break;
            }
            return "";
    }


    static function filtrarCamposDescripcion($alumnos)
    {
            $resultado = array();

            foreach ($alumnos as $alumno)
            {
                    $alumno[43] = utf8_decode(self::filtrarSituacionLaboral($alumno[43], $alumno));
                    $alumno[47] = utf8_decode(self::filtrarHorasTrabajo($alumno[47], $alumno));
                    $alumno[48] = utf8_decode(self::filtrarRelacionTrabajoEstudio($alumno[48], $alumno));

                    $alumno[49] = utf8_decode(self::filtrarSituacionLaboralPadre($alumno[49], $alumno));
                    $alumno[54] = utf8_decode(self::filtrarSituacionLaboralPadre($alumno[54], $alumno));

                    $alumno[50] = utf8_decode(self::filtrarUltimosEstudiosPadre($alumno[50], $alumno));
                    $alumno[55] = utf8_decode(self::filtrarUltimosEstudiosPadre($alumno[55], $alumno));

                    $alumno[51] = utf8_decode(self::filtrarActividadEconomicaPadre($alumno[51], $alumno));
                    $alumno[56] = utf8_decode(self::filtrarActividadEconomicaPadre($alumno[56], $alumno));

                    $resultado[] = $alumno;
            }

            return $resultado;
    }



    static public function agregaDatosCensales($db, $alumno)
    {
            $legajo = $alumno[4];
            $sql = "SELECT 
                                            *, mug_localidades.nombre AS loc_procedencia
                            FROM vw_datos_censales_actuales,
                                    OUTER mug_localidades
                            WHERE 
                                    vw_datos_censales_actuales.nro_inscripcion = '$legajo' AND
                                    vw_datos_censales_actuales.loc_proc = mug_localidades.localidad
                                    ";

            $datos = $db->query($sql)->fetchAll(PDO::FETCH_NUM);

            $alumno = array_merge($alumno, $datos[0]);
            return $alumno;
    }


    // Al listado le agrerega los datos personales y censales
    static public function agregaDatosCensalesPersonales($db, $alumnos)
    {
            $resultado = array();
            foreach ($alumnos as $alumno)
            {
                    $alumno = self::agregaDatosPersonales($db, $alumno);
                    $alumno = self::agregaDatosCensales($db, $alumno);

                    $resultado[] = $alumno;

            }
            return $resultado;
    }
		
    // Devuelve los ingresantes de un a�o/carrera
    static public function getAlumnosAnioAcademico($filtro)
    {
        $sql = "SELECT sga_personas.nro_inscripcion, 
                    sga_personas.apellido || ', ' || sga_personas.nombres AS nombre,					
                    sga_periodo_insc.anio_academico, 
                    sga_alumnos.carrera, 
                    sga_alumnos.legajo, 
                    sga_alumnos.fecha_ingreso
                FROM sga_alumnos, sga_periodo_insc, sga_personas
                WHERE sga_alumnos.nro_inscripcion = sga_personas.nro_inscripcion
                    AND sga_alumnos.fecha_ingreso BETWEEN sga_periodo_insc.fecha_inicio AND sga_periodo_insc.fecha_fin
                    AND sga_periodo_insc.tipo = 'I'";

        if (isset($filtro['anio_academico']))
        {
            $anio_academico = $filtro['anio_academico'];
            $sql .= " AND sga_periodo_insc.anio_academico = $anio_academico";
        }

        if (isset($filtro['carrera_destino']))
        {
            $carrera = $filtro['carrera_destino'];
            $sql .= " AND sga_alumnos.carrera = '$carrera'";
        }

        $db = self::getConexion ();
        $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_NUM);
        $alumnos = self::addOrigen($db, $alumnos);

        if (isset($filtro['filtra_materias_primero']) && ($filtro['filtra_materias_primero'] == '1'))
        {
            $alumnos = self::filtrarConUnaMateria($db, $alumnos);
        }

        $alumnos = self::agregaDatosCensalesPersonales($db, $alumnos);
        $alumnos = self::filtrarCamposDescripcion($alumnos);
        $alumnos = self::addFakeId($alumnos);
        return $alumnos;
    }
		
    /**
    * Retorna un listado de los alumnos que desprobaron el curso de ingreso un determinado a�o.
    */
    static function get_curso_ingreso_desaprobados($anio)
    {
            $db = MisConsultas::getConexionMini($anio);
            $sql = "SELECT A.carrera, P.apellido || ', ' || P.nombres AS alumno, D.desc_abreviada AS tipo_documento, P.nro_documento
                                    FROM sga_alumnos A
                                            JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                                            JOIN sga_carrera_aspira S ON (A.unidad_academica = S.unidad_academica AND A.nro_inscripcion = S.nro_inscripcion AND A.carrera = S.carrera)
                                            LEFT JOIN mdp_tipo_documento D ON (P.tipo_documento = D.tipo_documento)
                                    WHERE S.periodo_inscripcio = '$anio'
                                        AND A.carrera NOT IN ('211', '290')
                                        AND A.legajo NOT IN 
                                            (SELECT legajo FROM vw_hist_academica V 
                                                WHERE A.unidad_academica = V.unidad_academica 
                                                    AND A.carrera = V.carrera 
                                                    AND V.resultado = 'A')
                                        AND A.legajo IN 
                                            (SELECT legajo FROM vw_hist_academica V 
                                                WHERE A.unidad_academica = V.unidad_academica 
                                                    AND A.carrera = V.carrera 
                                                    AND V.resultado = 'R')
                            ORDER BY 2";

            $desaprobados = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $desaprobados;
    }

		
    /**
    * -- Listado de alumnos cuyo �ltimo a�o de reinscripci�n es el pasado por par�metro
    */
    function get_reinscriptos_sin_actividad($filtro)
    {
            $anio_reinscripcion = $filtro['anio_academico'];

            $sql = "SELECT          P.apellido || ', ' || P.nombres AS alumno, 
                                    P.nro_documento, 
                                    YEAR(A.fecha_ingreso) AS fecha_ingreso,
                                    A.carrera,
                                    A.legajo, 
                                    V.te_per_lect AS telefono, 
                                    V.e_mail
                                    FROM    sga_alumnos A
                                            JOIN sga_reinscripcion R ON (A.unidad_academica = R.unidad_academica AND A.carrera = R.carrera AND A.legajo = R.legajo)
                                            JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                                            LEFT JOIN vw_datos_censales_actuales V ON (V.unidad_academica = P.unidad_academica AND V.nro_inscripcion = P.nro_inscripcion)
                                    WHERE  A.calidad = 'A'
                                    GROUP BY 1,2,3,4,5,6,7
                                    HAVING MAX (R.anio_academico) = $anio_reinscripcion
                    ORDER BY alumno";

            $db = MisConsultas::getConexion();
            $reinscriptos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $resultado = array();
            foreach ($reinscriptos as $alumno)
            {
                    $legajo = $alumno['LEGAJO'];
                    $carrera = $alumno['CARRERA'];
                    $alumno = self::agregaAnioUltimaActividad($db, $alumno, $legajo, $carrera);
                    $alumno = self::agregaPorcentajeAvance($db, $alumno, $legajo, $carrera);
                    $resultado[] = $alumno;
            }

            $resultado = self::addFakeId($resultado);
            return $resultado;
    }		

    static public function agregaPorcentajeAvance($db, $alumno, $legajo, $carrera)
    {
        $sql = "EXECUTE PROCEDURE sp_porc_exa('EXA', $carrera, $legajo)";
        $datos = $db->query($sql)->fetchAll(PDO::FETCH_NUM);
        $alumno['PORCENTAJE_AVANCE'] = $datos[0][0];
        return $alumno;
    }		

    static public function agregaAnioUltimaActividad($db, $alumno, $legajo, $carrera)
    {
        $sql = "SELECT MAX(fecha) AS ultima_actividad
                    FROM vw_hist_academica
                    WHERE 
                            vw_hist_academica.legajo = '$legajo' AND
                            vw_hist_academica.carrera = '$carrera'
                UNION 
                    SELECT MAX(fecha_regularidad) AS fecha
                    FROM sga_cursadas
                    WHERE 
                            sga_cursadas.legajo = '$legajo' AND
                            sga_cursadas.carrera = '$carrera'";

        $datos = $db->query($sql)->fetchAll(PDO::FETCH_NUM);

        if (count($datos) > 0)
        {
            if (count($datos) == 1)
            {
                $ultima = $datos[0][0];
            } else
            {
                $ultima = ($datos[0][0] > $datos[1][0]) ? $datos[0][0] : $datos[1][0];
            }

            $alumno["ULTIMA_ACTIVIDAD"] = $ultima;
        }
        return $alumno;
    }		

    static public function agregaTelefonoEmail($db, $alumno, $legajo)
    {
            $sql = "SELECT te_per_lect AS telefono, e_mail
                        FROM vw_datos_censales_actuales
                        WHERE vw_datos_censales_actuales.nro_inscripcion = '$legajo'";

            $datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if (count($datos) > 0)
            {
                    $datos = $datos[0];
                    $alumno = array_merge($alumno, $datos);
            }
            return $alumno;
    }		

    /**
		* -- Alumnos que NO se reinscribieron mas a partir de un a�o, CON O SIN ACTIVIDAD
		*/
		static function get_alumnos_sin_reinscripcion($filtro)
		{
			$anio_ingreso = $filtro['anio_ingreso'];
			
			$db = MisConsultas::getConexion();
			
			//31 de mayo Andrea corre el control de pasar a no regular los que no se matricularon, así que hay que tomar ese umbral.
			$sql = "SELECT (P.apellido || ', ' || P.nombres) AS nombres, 
						P.nro_documento, 
						YEAR(A.fecha_ingreso) AS ingreso,
						A.carrera,
						A.legajo, 
						CASE 	WHEN A.calidad = 'A' THEN 'Activo'
								WHEN A.calidad = 'P' THEN 'Pasivo'
								WHEN A.calidad = 'N' THEN 'Abandono'
						END AS calidad, 
						R.anio_academico AS ultima_reinscrip
					FROM sga_alumnos A, sga_anio_academico C, sga_personas P, sga_reinscripcion R
					WHERE A.fecha_ingreso BETWEEN C.fecha_inicio AND C.fecha_fin 
						AND C.anio_academico = $anio_ingreso
						AND A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion
						AND A.unidad_academica = R.unidad_academica AND A.carrera = R.carrera AND A.legajo = R.legajo
						AND A.calidad <> 'E'
						AND A.carrera <> 290						
						AND TRIM(A.legajo)||TRIM(A.carrera) NOT IN (
						SELECT TRIM(R2.legajo)||TRIM(R2.carrera)
							FROM sga_reinscripcion R2
							WHERE R2.carrera = A.carrera AND
							R2.legajo = A.legajo AND
							( ( R2.anio_academico >= YEAR(TODAY) AND TODAY > MDY(5,31, YEAR(TODAY)) )
								OR ( R2.anio_academico >= (YEAR(TODAY)-1) AND TODAY <= MDY(5,31, YEAR(TODAY)) )
							)
						)
						AND R.anio_academico = (
							      SELECT MAX(R3.anio_academico) 
								FROM sga_reinscripcion R3
								WHERE R.unidad_academica = R3.unidad_academica  
									AND R.carrera = R3.carrera 
									AND R.legajo = R3.legajo)";

			if (isset($filtro['carrera']))
			{
				$carrera = $filtro['carrera'];
				$sql .= " AND A.carrera = $carrera";
			}
					
			$sql .= " ORDER BY R.anio_academico";
			
			$reinscriptos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			
			$resultado = array();
			foreach ($reinscriptos as $alumno)
			{
				$legajo = $alumno['LEGAJO'];
				$carrera = $alumno['CARRERA'];
				$alumno = self::agregaTelefonoEmail($db, $alumno, $legajo);
				$alumno = self::agregaAnioUltimaActividad($db, $alumno, $legajo, $carrera);
				$resultado[] = $alumno;
			}
			
			$resultado = self::addFakeId($resultado);
			
			return $resultado;
		}		
	
		static function getAlumnosAddFiltroBusqueda($filtro)
		{
			$filtro = strtoupper($filtro['busqueda']);
				
			return " AND
					(UPPER(sga_personas.nombres) LIKE '%$filtro%' OR
					 UPPER(sga_personas.apellido) LIKE '%$filtro%' OR
					 UPPER(sga_personas.nro_documento) LIKE '%$filtro%' OR
					 UPPER(sga_alumnos.legajo) LIKE '%$filtro%' OR
					 UPPER(sga_carreras.carrera) LIKE '%$filtro%' OR
					 UPPER(sga_carreras.nombre) LIKE '%$filtro%')";			
		}

		static function getAlumnosAddFiltroDepartamento($departamento)
		{
			$filtro = strtoupper($departamento);
				
			return " AND (sga_carreras.departamento = '$filtro') ";			
		}		
		
                static function getAlumnosAddFiltroCarrera($carrera)
		{
        		return " AND (sga_carreras.carrera = '$carrera') ";			
		}	
		
                static function getAlumnosAddFiltroRegularesActivos()
		{
        		return " AND (sga_alumnos.regular = 'S' AND sga_alumnos.calidad = 'A') ";
		}
                
		static function buildSQLAlumnos()
		{
			$sql = "SELECT 	DISTINCT
						sga_personas.apellido || ', ' || sga_personas.nombres AS nombre,
						sga_personas.nro_inscripcion,
                                                sga_personas.nro_documento,
                                                sga_alumnos.legajo,
						vw_datos_censales_actuales.e_mail, 
                                                sga_personas.fecha_nacimiento 
                                                
						FROM sga_alumnos, sga_personas, sga_carreras, vw_datos_censales_actuales
						WHERE 
							sga_alumnos.nro_inscripcion = sga_personas.nro_inscripcion AND
							sga_alumnos.carrera = sga_carreras.carrera AND
							sga_alumnos.nro_inscripcion = vw_datos_censales_actuales.nro_inscripcion";
							
			return $sql;
		}

		static function getAlumnosPosgrado($filtro)
		{
			$db = MisConsultas::getConexionPostgrado();
			
			$sql = MisConsultas::buildSQLAlumnos();
			
			if (isset($filtro['departamento']))
			{
				$sql .= MisConsultas::getAlumnosAddFiltroDepartamento($filtro['departamento']);
			}							
			
			if (isset($filtro['busqueda']))
			{
				$sql .= MisConsultas::getAlumnosAddFiltroBusqueda($filtro);
			}
								
			$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			$alumnos = self::addFakeId($alumnos);
			
			return $alumnos;
		}
		
		static function getAlumnos($filtro)
		{
			$db = MisConsultas::getConexion();
			
			$sql = MisConsultas::buildSQLAlumnos();
			
			if (isset($filtro['departamento']))
			{
				$sql .= MisConsultas::getAlumnosAddFiltroDepartamento($filtro['departamento']);
			}							
			
                        if (isset($filtro['carrera']))
			{
				$sql .= MisConsultas::getAlumnosAddFiltroCarrera($filtro['carrera']);
			}
			
                        if (isset($filtro['busqueda']))
			{
				$sql .= MisConsultas::getAlumnosAddFiltroBusqueda($filtro);
			}
				
                        if (isset($filtro['regular_activo']))
                        {
                            $sql .= MisConsultas::getAlumnosAddFiltroRegularesActivos();
                        }
			$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			$alumnos = self::addFakeId($alumnos);
			
			return $alumnos;
		}
                
		static function getTipoConexion($esGrado)
		{
			if ($esGrado)
			{
				$db = MisConsultas::getConexion();
			} else
			{
				$db = MisConsultas::getConexionPostgrado();
			}
			return $db;
		}

		static function getUnidadAcademica($esGrado)
		{
			if ($esGrado)
			{
				$ua = 'EXA';
			} else
			{
				$ua = 'EXAP';
			}
			return $ua;
		}
		
        static function getHistoriaAcademica($parametros, $esGrado = true)
        {
            $nro_inscripcion = $parametros['NRO_INSCRIPCION'];
            
            $db = MisConsultas::getTipoConexion($esGrado);
            $ua = MisConsultas::getUnidadAcademica($esGrado);
            
            $sql = "EXECUTE PROCEDURE sp_fichaluhisacad( '$ua', '$nro_inscripcion' )";
            
            $historia = $db->query($sql)->fetchAll();
            $historia = self::addFakeId($historia);
    
            return $historia;
            
        }
        
        static function getCursadas($parametros, $esGrado = true)
        {
            $nro_inscripcion = $parametros['NRO_INSCRIPCION'];

            $db = MisConsultas::getTipoConexion($esGrado);
            
            $sql = "SELECT sga_cursadas.materia,"
                    . "sga_cursadas.fecha_regularidad,"
                    . "sga_cursadas.resultado,"
                    . "sga_cursadas.nota,"
                    . "sga_cursadas.fin_vigencia_regul,"
                    . "sga_cursadas.carrera,"
                    . "sga_materias.nombre AS nombre_materia,"
                    . "sga_carreras.nombre AS nombre_carrera"
                    . " FROM sga_cursadas, sga_carreras, sga_materias, sga_alumnos"
                    . " WHERE "
                    . " sga_alumnos.nro_inscripcion = '$nro_inscripcion' "
                    . " AND sga_alumnos.carrera = sga_cursadas.carrera"
                    . " AND sga_alumnos.legajo = sga_cursadas.legajo"
                    . " AND sga_cursadas.carrera = sga_carreras.carrera"
                    . " AND sga_cursadas.materia = sga_materias.materia";

            if (isset($parametros['CARRERA']))
            {
                $carrera = $parametros['CARRERA'];
                $sql .= " AND sga_alumnos.carrera = '$carrera' ";
            }
            $cursadas = $db->query($sql)->fetchAll();
            $cursadas = self::addFakeId($cursadas);
    
            return $cursadas;
        }   

        static function getInscripcionCursadas($parametros, $esGrado = true)
        {
            $nro_inscripcion = $parametros['NRO_INSCRIPCION'];

            $db = MisConsultas::getTipoConexion($esGrado);
            
            $sql = "SELECT 	I.carrera, 
                            CAR.nombre AS carrera_nombre,
                            I.comision,
                            M.materia,
                            M.nombre AS materia_nombre,
                            DECODE(I.calidad_insc, 'R', 'Regular', 'P', 'Promocional') AS calidad_insc,
                            DECODE (I.estado, 'A', 'Aceptada', 'E', 'Aceptada c/excep', 'P', 'Pendiente', 'R', 'Rechazada') as estado
                        FROM sga_insc_cursadas I
                        JOIN sga_comisiones C ON (C.comision = I.comision)
                        JOIN sga_periodos_lect P ON (P.anio_academico = C.anio_academico AND P.periodo_lectivo = C.periodo_lectivo)
                        JOIN sga_materias M ON (M.materia = C.materia)
                        JOIN sga_carreras CAR ON (I.carrera = CAR.carrera)
                        WHERE I.legajo = '$nro_inscripcion'
                        AND I.comision = C.comision 
                        AND TODAY BETWEEN P.fecha_inicio AND P.fecha_fin ";

            if (isset($parametros['CARRERA']))
            {
                $carrera = $parametros['CARRERA'];
                $sql .= " AND I.carrera = '$carrera' ";
            }
            $insc_cursadas = $db->query($sql)->fetchAll();
            $insc_cursadas = self::addFakeId($insc_cursadas);

            return $insc_cursadas;
        }   
        
        static function getCarrerasPlan($parametros, $esGrado = true)
        {
            $nro_inscripcion = $parametros['NRO_INSCRIPCION'];
            
            $db = MisConsultas::getTipoConexion($esGrado);
            
            $sql = "SELECT sga_carreras.nombre as carrera, sga_alumnos.plan, 
                    sga_alumnos.fecha_ingreso, sga_alumnos.regular, sga_alumnos.calidad, 
                    sga_carreras.carrera as carrera_codigo
                    FROM sga_alumnos, sga_carreras
                    WHERE 
                            sga_alumnos.carrera = sga_carreras.carrera
                            AND nro_inscripcion = '$nro_inscripcion';";

            $cursadas = $db->query($sql)->fetchAll();
            $cursadas = self::addFakeId($cursadas);
            return $cursadas;
        }   
        
        //devuelve el porcentaje de avance de un alumno en una determinada carrera
        static function add_porcentaje_avance($nro_inscripcion, $carrera, $esGrado = true)
        {
            $db = MisConsultas::getTipoConexion($esGrado);
            $sql = "EXECUTE PROCEDURE sp_porc_exa('EXA', '$carrera', '$nro_inscripcion')";
            $porc_avance = $db->query($sql)->fetchAll();
            return $porc_avance[0][0];                    
        }

        /**
         * Resultados para mesa - carrera - A, R,U
         * @param type $filtro
         * @return string
         */
        static private function prepareFiltrGetResultadosPorMesaCarrera($filtro)
        {
            $where = array();
            if (isset($filtro['anio_academico']))
            {
                $where[] = "sga_actas_examen.anio_academico = '".$filtro['anio_academico']."'";
            }
            if (isset($filtro['turno_examen']))
            {
                $where[] = "sga_actas_examen.turno_examen = '".$filtro['turno_examen']."'";
            }
            if (isset($filtro['llamado']))
            {
                $where[] = "sga_actas_examen.llamado = '".$filtro['llamado']."'";
            }
            if (isset($filtro['materia']))
            {
                $where[] = "sga_actas_examen.materia = '".$filtro['materia']."'";
            }
            if (isset($filtro['carrera']))
            {
                $carrera = $filtro['carrera'];
//                        $where[] = "sga_actas_examen.materia IN (
//                            SELECT materia FROM 
//                            sga_atrib_mat_plan WHERE
//                            carrera = '$carrera')";
                $where[] = "sga_detalle_acta.carrera = '$carrera'";                        
            }  

            if (count($where) > 0)
            {
                $where = implode(' AND ', $where);
                $where = "AND " . $where; 
            } else
            {
                $where = '';
            }
            
            return $where;
        }
        
        static function realizaConsultaResultadosPorMesaCarrera($db, $where)
        {
            $sql = "SELECT  sga_actas_examen.anio_academico,
                            sga_actas_examen.turno_examen,
                            sga_actas_examen.llamado,
                            sga_materias.nombre AS materia, 
                            sga_detalle_acta.resultado, 
                            COUNT(*) AS cantidad
                FROM sga_actas_examen, sga_detalle_acta, sga_materias
                WHERE 
                            sga_actas_examen.materia = sga_materias.materia AND
                            sga_actas_examen.tipo_acta = sga_detalle_acta.tipo_acta AND
                            sga_actas_examen.acta = sga_detalle_acta.acta 
                            $where
                GROUP BY sga_actas_examen.anio_academico,
                        sga_actas_examen.turno_examen,
                        sga_actas_examen.llamado,sga_materias.nombre, 
                        sga_detalle_acta.resultado ";
            $resultado = $db->query($sql)->fetchAll();
            return $resultado;
        }
        
        static function parsearResultadosPorMesaCarrera($filas)
        {
            $output = array();
            foreach ($filas as $fila)
            {
                $materia = $fila['MATERIA'];
                $resultado = $fila['RESULTADO'];
                $cantidad = $fila['CANTIDAD'];
                if (!isset($output[$materia]))
                {
                    $output[$materia] = array('Aprobados' => 0, 'Reprobados'=>0, 'Ausentes'=>0);
                }
                switch ($resultado) {
                    case 'A': $output[$materia]['Aprobados'] = $cantidad; break;
                    case 'R': $output[$materia]['Reprobados'] = $cantidad; break;
                    case 'U': $output[$materia]['Ausentes'] = $cantidad; break;
                    default:
                        break;
                }
                $output[$materia]['anio'] = $fila['ANIO_ACADEMICO'];
                $output[$materia]['turno'] = $fila['TURNO_EXAMEN'];
                $output[$materia]['llamado'] = $fila['LLAMADO'];
            }
            return $output;
        }

        static function prepararParaResultSetResultadosPorMesaCarrera($filas)
        {
            $resultado = array();
            $i = 0;
            foreach ($filas as $materia => $resultados)
            {
                $resultado[] = array (
                                        'MATERIA' => $materia,
                                        'APROBADOS' => $resultados['Aprobados'],
                                        'REPROBADOS' => $resultados['Reprobados'],
                                        'AUSENTES' => $resultados['Ausentes'],
                                        'ANIO' => $resultados['anio'],
                                        'TURNO' => $resultados['turno'],
                                        'LLAMADO' => $resultados['llamado']);
            }
            
            return $resultado;
        }
        
        public static function getResultadosPorMesaCarrera($filtro)
        {
            $db = MisConsultas::getConexion();
            
            $where = self::prepareFiltrGetResultadosPorMesaCarrera($filtro);
            $resultado = self::realizaConsultaResultadosPorMesaCarrera($db, $where);
            $resultado = self::parsearResultadosPorMesaCarrera($resultado);
            $resultado = self::prepararParaResultSetResultadosPorMesaCarrera($resultado);
            $resultado = MisConsultas::addFakeId($resultado);
            return $resultado;
        }
        
        
        static public function getReporteRecursadas($filtro)
        {
            $db = MisConsultas::getConexion();
            
            $sql = "SELECT sga_insc_cursadas.legajo, sga_comisiones.materia, sga_materias.nombre AS nombreMateria, COUNT(*) as cantidad
            FROM sga_insc_cursadas, sga_comisiones, sga_materias
            WHERE ";
            
            if (isset($filtro['carrera']))
            {
                $sql .= "sga_insc_cursadas.carrera = '".$filtro['carrera']."' AND ";
            }

            if (isset($filtro['materia']))
            {
                $sql .= "sga_materias.materia = '".$filtro['materia']."' AND ";
            }
            
            $sql .= " sga_comisiones.materia = sga_materias.materia AND
                sga_insc_cursadas.comision = sga_comisiones.comision
            GROUP BY sga_insc_cursadas.legajo, sga_comisiones.materia, sga_materias.nombre					
            HAVING COUNT(*) >= 1";
            
            $cursadas = $db->query($sql)->fetchAll();
            $cursadas = self::addFakeId($cursadas);
    
            return $cursadas;
        }
        
        public function getReporteRegularesAnioSexoCarrera($filtro)
        {
            $db = MisConsultas::getConexion();
            
            $anio_desde = $filtro['anio_desde'];
            $anio_hasta = $filtro['anio_hasta'];
            
            $sql = "SELECT legajo, YEAR(fecha_ingreso) AS anio_academico, 
                sga_carreras.nombre AS carrera_nombre, 
                DECODE(sga_personas.sexo, 1, 'M', 2, 'F') AS sexo
                FROM sga_alumnos, sga_personas, sga_carreras
                WHERE sga_alumnos.legajo = sga_personas.nro_inscripcion
                AND sga_alumnos.carrera = sga_carreras.carrera
                AND YEAR(fecha_ingreso) BETWEEN $anio_desde AND $anio_hasta
                UNION
                SELECT legajo, anio_academico, sga_carreras.nombre AS carrera_nombre, 
                DECODE(sga_personas.sexo, 1, 'M', 2, 'F') AS sexo
                FROM sga_reinscripcion, sga_personas, sga_carreras
                WHERE sga_reinscripcion.legajo = sga_personas.nro_inscripcion
                AND sga_reinscripcion.carrera = sga_carreras.carrera
                AND anio_academico BETWEEN $anio_desde AND $anio_hasta";
            
            $resultado = $db->query($sql)->fetchAll();
            
            $resultado = self::addFakeId($resultado);
    
            return $resultado;
        }
				
        static function inscriptos_a_cursar_cohorte($filtro)
        {
            $db = MisConsultas::getConexion();
                    
            $carrera = $filtro['CARRERA'];
            $materia = $filtro['MATERIA'];
			$cohorte = $filtro['COHORTE'];
			$anio_comision = $filtro['ANIO_ACADEMICO'];
			$periodo_lectivo = $filtro['PERIODO_LECTIVO'];
                    
            $sql = "SELECT sga_personas.apellido || ', ' || sga_personas.nombres AS alumno
					FROM sga_insc_cursadas 
					LEFT JOIN sga_comisiones ON (sga_insc_cursadas.comision = sga_comisiones.comision)
					LEFT JOIN sga_alumnos ON (sga_insc_cursadas.carrera = sga_alumnos.carrera AND
									sga_insc_cursadas.legajo = sga_alumnos.legajo)
					LEFT JOIN sga_carrera_aspira ON (sga_carrera_aspira.carrera = sga_alumnos.carrera AND
									sga_carrera_aspira.nro_inscripcion = sga_alumnos.nro_inscripcion)
					LEFT JOIN sga_personas ON (sga_carrera_aspira.nro_inscripcion = sga_personas.nro_inscripcion)
					WHERE 
					sga_carrera_aspira.carrera = '$carrera' AND
					sga_comisiones.materia = '$materia' AND 
					sga_comisiones.anio_academico = '$anio_comision' AND 
					sga_comisiones.periodo_lectivo = '$periodo_lectivo' AND 
					YEAR(sga_alumnos.fecha_ingreso) = $cohorte AND
					sga_alumnos.legajo NOT IN 
                                        (SELECT legajo FROM vw_hist_academica 
                                                WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
                                                AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = sga_alumnos.carrera
										) AND
                    sga_alumnos.legajo NOT IN 
                                        (SELECT legajo FROM sga_cursadas 
                                                WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
                                                AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = sga_alumnos.carrera);";
				
		
            $resultado = $db->query($sql)->fetchAll();
                    
            $resultado = self::addFakeId($resultado);
			
            return $resultado;			
        }                

        static function inscriptos_a_cursar($filtro)
        {
            $db = MisConsultas::getConexion();
                    
            $carrera = $filtro['CARRERA'];
            $materia = $filtro['MATERIA'];
            $anio_comision = $filtro['ANIO_ACADEMICO'];
            $periodo_lectivo = $filtro['PERIODO_LECTIVO'];
                    
            $sql = "SELECT  sga_personas.apellido || ', ' || sga_personas.nombres AS alumno,
                            sga_alumnos.legajo
                        FROM sga_insc_cursadas 
                        JOIN sga_comisiones ON (sga_insc_cursadas.comision = sga_comisiones.comision)
                        JOIN sga_alumnos ON (sga_insc_cursadas.carrera = sga_alumnos.carrera AND
                                                        sga_insc_cursadas.legajo = sga_alumnos.legajo)
                        JOIN sga_personas ON (sga_alumnos.nro_inscripcion = sga_personas.nro_inscripcion)
                            WHERE   sga_comisiones.anio_academico = '$anio_comision' AND 
                                    sga_comisiones.periodo_lectivo = '$periodo_lectivo' AND 
                                    sga_alumnos.carrera = '$carrera' AND
                                    sga_comisiones.materia = '$materia' 
                    ORDER BY 1 ";
		
            $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;			
        }                

    }

        
        
?>
