<?php
require_once 'MisConsultas.php';

class ci_detalle_alumnos_mat_aprob extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro))
                {
                    $filtro = $this->s__datos_filtro;
                    $datos = $this->get_cursadas_aprobadas($filtro);
                    $cuadro->set_datos($datos);
		} 
                else 
                {
                    $cuadro->limpiar_columnas();
		}
	}

	function get_cursadas_aprobadas($filtro)
	{
            $carrera = $filtro['CARRERA'];

            $sql = "SELECT  D.legajo, 
                            P.apellido || ', ' || P.nombres AS alumno, 
							D.anio_ingreso,
							D.plan,
                            M.nombre || ' (' || M.materia || ')' AS nombre_materia, 
                            fecha_regularidad, 
                            CASE D.forma_aprob_cursada	
                                    WHEN 'P' THEN 'Promoción'
                                    WHEN 'C' THEN 'Cursada'
                                    WHEN 'CE' THEN 'Cursada Equivalente'
                                    WHEN 'E' THEN 'Equivalencia'
                                    WHEN 'EE' THEN 'Equivalencia Equivalente'
                                    ELSE ''
                            END AS forma_aprob_cursada,
                            fecha_examen,
                            forma_aprob_final
                                    FROM rep_detalle_alumnos_mat_aprob D
                                    JOIN sga_personas P ON (P.nro_inscripcion = D.legajo)
                                    JOIN sga_materias M ON (D.materia = m.materia)
                                    WHERE D.carrera = $carrera";
            
            if (isset ($filtro['MATERIA']))
            {
                $materia = $filtro['MATERIA'];
                $sql .= " AND D.materia = '$materia'";
            }
            
            $sql .= " ORDER BY 2";
        
            $db = MisConsultas::getConexion();
            $datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
	}

    //-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_detalle_alumnos_mat_aprob'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE sp_rep_detalle_alumnos_mat_aprob()";
        $db->query($sql)->fetchAll();
    }

}
?>