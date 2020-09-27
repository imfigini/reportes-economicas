<?php
require_once('MisConsultas.php');

class ConsultasCursadas 
{
    
    /** Retorna el listado de materias de una determinada carrera pertenecientes al plan activo vigente **/
    static function get_materias($carrera) 
    {
        $sqlText = "SELECT DISTINCT M.materia, M.nombre_reducido || ' (' || M.materia || ')' AS nombre
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

    //Retorna todos los alumnos que cursaron una determinada materia en un determinado año
    static function get_alumnos_cursada($materia=NULL, $anio_cursada=NULL, $carrera=NULL, $condicion=NULL, $sede=null)
    {
        $sql = "SELECT  A.legajo, 
                        D.apellido || ', ' || D.nombres AS alumno, 
                        X.nombre || ' (' || A.carrera || ')' AS carrera,
                        M.nombre || ' (' || A.materia || ')' AS materia,
                        A.fecha_regularidad, 
                        A.resultado, 
                        A.nota, 
                        AA.regular, 
                        AA.calidad,
                        S.nombre as sede
                            FROM sga_cursadas A
                            JOIN sga_comisiones C ON (A.unidad_academica = C.unidad_academica AND A.comision = C.comision)
                            JOIN sga_sedes S ON (S.sede = C.sede)
                            JOIN sga_carrera_aspira B ON (A.unidad_academica = B.unidad_academica AND A.carrera = B.carrera AND A.legajo = B.nro_inscripcion)
							JOIN sga_alumnos L ON (B.unidad_academica = L.unidad_academica AND B.carrera = L.carrera AND B.nro_inscripcion = L.nro_inscripcion)
                            JOIN sga_personas D ON (D.unidad_academica = B.unidad_academica AND D.nro_inscripcion = B.nro_inscripcion)
                            JOIN sga_materias M ON (A.materia = M.materia)
                            JOIN sga_carreras X ON (A.carrera = X.carrera)
                            JOIN sga_alumnos AA ON (A.unidad_academica = AA.unidad_academica AND A.legajo = AA.legajo AND A.carrera = AA.carrera) ";
        

        if (isset($materia))
        {
            $sql .= " AND A.materia = '$materia'";
        }
        if (isset($anio_cursada))
        {
            $sql .= " AND C.anio_academico = '$anio_cursada'";
        }
        if (isset($carrera))
        {
            $sql .= " AND A.carrera = '$carrera'";
        }

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
        
        if (isset($sede))
        {
            $sql .= " AND C.sede = '$sede'";
        }

        $sql .= " ORDER BY alumno";
		
	//ei_arbol(array($sql));
		
        $db = MisConsultas::getConexion ();
		
        $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //ei_arbol($alumnos);
        return $alumnos;
    }
}