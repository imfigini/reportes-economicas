<?php
require_once('MisConsultas.php');

class ci_cantidad_rebotes extends toba_ci
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

	function get_cantidad_rebotes($carrera)
	{
		$sql = "SELECT  V.legajo, 
                                P.apellido || ', ' || P.nombres AS alumno, 
                                V.carrera, 
                                COUNT(*) AS CANT_REBOTES
					FROM sga_alumnos A
					JOIN vw_hist_academica V ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo AND A.regular = 'S' AND A.calidad = 'A')
                                        JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					WHERE V.resultado = 'R'";

		if (isset($carrera))
			$sql .= " AND V.carrera = $carrera";

		$sql .= " GROUP BY 1,2,3
				ORDER BY 3 DESC";
				
		$db = MisConsultas::getConexion ();
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;	
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$datos = $this->get_cantidad_rebotes($filtro['NOMBRE']);
			$cuadro->set_datos($datos);
		}
		else 
		{
			$datos = $this->get_cantidad_rebotes(NULL);
			$cuadro->set_datos($datos);
		}
	}

}

?>