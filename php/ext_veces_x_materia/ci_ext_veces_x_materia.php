<?php
require_once('MisConsultas.php');

class ci_ext_veces_x_materia extends toba_ci
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

	//Número de veces que se solicita extensión para cada asignatura de plan
	function get_veces_x_materia($carrera)
	{
		$db = MisConsultas::getConexion();
		$sql = 'EXECUTE PROCEDURE "dba".sp_rep_ext_veces_x_materia('.$carrera.')';
		$result = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$datos = $this->get_veces_x_materia($filtro['NOMBRE']);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$datos = $this->get_veces_x_materia('NULL');
			$cuadro->set_datos($datos);
		}
	}

}

?>