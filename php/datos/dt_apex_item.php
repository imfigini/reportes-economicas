<?php
class dt_apex_item extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT item, nombre FROM apex_item ORDER BY nombre";
		return toba::db('toba_2_6')->consultar($sql);
	}

}

?>