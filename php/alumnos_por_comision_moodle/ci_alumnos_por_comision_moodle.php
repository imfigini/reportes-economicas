<?php
require_once("MisConsultas.php");

class ci_alumnos_por_comision_moodle extends toba_ci
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
                $datos_alumnos = MisConsultas::getInscriptosMateriaAnioPeriodo($this->s__datos_filtro);
                $cuadro->set_datos($datos_alumnos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	function conf__cuadro_doc(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__datos_filtro)) 
            {
                $datos_docentes = MisConsultas::getDocentesMateriaAnioPeriodo($this->s__datos_filtro);
                $cuadro->set_datos($datos_docentes);

            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}
	
	function get_sedes()
	{
		$sql = "SELECT sede, nombre FROM sga_sedes";
		$db = MisConsultas::getConexion ();
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

}

?>