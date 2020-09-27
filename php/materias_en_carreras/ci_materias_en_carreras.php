<?php
require_once ('MisConsultas.php');

class ci_materias_en_carreras extends toba_ci
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
                $descripcion = $this->s__datos_filtro['descripcion'];
                $datos = self::get_carreras($descripcion);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	//---- Funciones ---------------------------------------------------------------------
        
        static function get_carreras($descripcion)
        {
            $sql = "SELECT  DISTINCT materia, 
                            nombre_materia, 
                            C.nombre || ' (' || C.carrera || ')' AS carrera
                        FROM sga_carreras C
                        JOIN sga_atrib_mat_plan A ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
                    WHERE C.estado = 'A'
                        AND (materia LIKE '%$descripcion%' OR lower(nombre_materia) LIKE lower('%$descripcion%')) ";
            $db = MisConsultas::getConexion();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
}

?>