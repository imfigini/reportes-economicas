<?php
require_once 'consultas_extension.php';

class ci_alumnos_cohorte extends toba_ci
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
			$datos = consultas_extension::get_alumnos_ingresantes_cohorte($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}


	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Atrapa la interacción del usuario con el botón asociado
	 * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuración
	 */
	 //Actualiza la tabla "rep_alumnos_cohorte" para hacer más eficientes el resto de las consultas 
	function evt__formulario__actualizar($datos)
	{
            try
            {
                $db = MisConsultas::getConexion();
                $sql = "EXECUTE PROCEDURE dba.sp_alumnos_cohorte()";
                $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                die();
            }
	}

	//Retrona la fecha de última actualización de la tabla "rep_alumnos_cohorte"
	function get_ultima_actualizacion()
	{
            $db = MisConsultas::getConexion();
            $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                                    FROM rep_fecha_actualiz_tablas
                                            WHERE tabla = 'rep_alumnos_cohorte'";
            $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}
}