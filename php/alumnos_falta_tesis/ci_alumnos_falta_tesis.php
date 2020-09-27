<?php
require_once('MisConsultas.php');

class ci_alumnos_falta_tesis extends toba_ci
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

	function get_alumnos_falta_tesis($carrera=NULL)
	{
		$carrera = $carrera['carrera'];
		$sqlText = "SELECT  C.nombre || ' (' || R.carrera || ')' AS carrera, 
                                    R.legajo, 
                                    P.apellido || ', ' || P.nombres AS alumno,
                                    R.fecha_ingreso,
                                    R.fecha_ult_actividad,
                                    A.regular
				FROM rep_solo_falta_tesis R
				JOIN sga_personas P ON (R.legajo = P.nro_inscripcion) 
                                JOIN sga_alumnos A ON (A.nro_inscripcion = P.nro_inscripcion AND A.carrera = R.carrera)
                                JOIN sga_carreras C ON (R.carrera = C.carrera)";
		
		if (isset($carrera))
		{
			$sqlText .= " WHERE R.carrera = $carrera";
		}
                
                $sqlText .= " ORDER BY 1,3";
		
		$db = MisConsultas::getConexion ();
		$resultado = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
		return $resultado;
	}
		
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{	
			$datos = self::get_alumnos_falta_tesis($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}


    //-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_solo_falta_tesis'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE sp_rep_solo_falta_tesis()";
        $db->query($sql)->fetchAll();
    }
}

?>