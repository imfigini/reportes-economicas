<?php
require_once 'MisConsultas.php';

class ci_datos_censales extends toba_ci
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
                    $datos = $this->get_datos_censales($filtro);
                    $cuadro->set_datos($datos);
		} 
                else 
                {
                    $cuadro->limpiar_columnas();
		}
	}

    function get_datos_censales($filtro)
    {
        $anio_academico = $filtro['ANIO_ACADEMICO'];
        
        $sql = "SELECT DISTINCT D.*
                    FROM rep_datos_censales D
                    JOIN sga_carrera_aspira CA ON (CA.nro_inscripcion = D.nro_inscripcion)
                    JOIN sga_periodo_insc P ON (P.periodo_inscripcio = CA.periodo_inscripcio AND P.anio_academico = $anio_academico) ";
        if (isset($filtro['CARRERA']))
        {
            $carrera = $filtro['CARRERA'];
            $sql .= " WHERE CA.carrera = '$carrera' ";
        }
        
        $sql .= " UNION
                  SELECT DISTINCT D.*
                    FROM rep_datos_censales D
                    JOIN sga_alumnos A ON (A.nro_inscripcion = D.nro_inscripcion)
                    JOIN sga_reinscripcion R ON (R.unidad_academica = A.unidad_academica
                                                AND R.carrera = A.carrera
                                                AND R.legajo = A.legajo
                                                AND R.anio_academico = $anio_academico) ";
         
        if (isset($filtro['CARRERA']))
        {
            $carrera = $filtro['CARRERA'];
            $sql .= " WHERE A.carrera = '$carrera' ";
        }
        $sql .= " ORDER BY 2";

        $db = MisConsultas::getConexion();
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_datos_censales'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__formulario__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE sp_rep_datos_censales()";
        $db->query($sql)->fetchAll();
    }
}

?>