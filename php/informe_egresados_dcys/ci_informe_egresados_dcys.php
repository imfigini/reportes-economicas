<?php
require_once('MisConsultas.php');

class ci_informe_egresados_dcys extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__carreras = " ('206', '213', '212') ";  //Ing. Sistemas, TUDAI, TUPAR

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
			$anio = $filtro['ANIO'];
			$datos = $this->get_egresados($anio);
			$cuadro->set_datos($datos);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//---- Cuadro -----------------------------------------------------------------------
	
	static function getAniosAcademicos ($otroAnio = 2000) 
    {
        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT anio_academico
                        FROM sga_anio_academico";

        if (isset($otroAnio))
        {
            $sqlText .= " WHERE anio_academico >= $otroAnio";
        }

        $sqlText .= " ORDER BY 1 DESC";

        $anios = $db->query($sqlText);
        return $anios;
	}	
	
	function get_egresados($anio = null)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT 	YEAR(O.fecha_egreso) AS anio_egreso, 
						T.titulo, 
						T.nombre AS titulo_nombre, 	
						T.nivel, 
						REPLACE(AVG(O.prom_general)::VARCHAR(4),',','.') AS prom_con_aplazos, 
						REPLACE(AVG(O.prom_sin_aplazos)::VARCHAR(4),',','.') AS prom_sin_aplazos,
						REPLACE((AVG(O.fecha_egreso - ASP.fecha_inscripcion)/365)::VARCHAR(5),',','.') AS prom_duracion,
						COUNT(legajo) AS cant_egresados
				FROM sga_titulos_otorg O, sga_titulos T, sga_carrera_aspira ASP
				WHERE 	O.titulo = T.titulo
						AND O.carrera IN $this->s__carreras 
						AND ASP.unidad_academica = O.unidad_academica
						AND ASP.carrera = O.carrera
						AND ASP.nro_inscripcion = O.nro_inscripcion ";

		if (isset($anio)) {
			$sql .= " AND YEAR(O.fecha_egreso) = $anio ";
		} else {
			$sql .= " AND YEAR(O.fecha_egreso) >= 2000 ";
		}
		$sql .= " GROUP BY 1,2,3,4
				  ORDER BY 1,3 ";

		//ei_arbol($sql);
		$egresados = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $egresados;

	}
}

?>