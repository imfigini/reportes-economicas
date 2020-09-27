<?php

/**
 * Muestra un Arbol donde el usuario puede colapsar/descolapsar niveles
 * Estos niveles se pueden cargar por adelantado o hacer una cargar AJAX
 * Cada nodo debe implementar la interfaz toba_nodo_arbol
 *
 * @see toba_nodo_arbol
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_arbol ei_arbol
 * @wiki Referencia/Objetos/ei_arbol
 */
class my_ei_arbol extends toba_ei_arbol
{
    public function mostrar_nodo(toba_nodo_arbol $nodo, $es_visible)
	{
		$salida = $this->mostrar_utilerias($nodo);
		$salida .= $this->mostrar_cambio_expansion($nodo, $es_visible);
		$salida .= $this->mostrar_iconos($nodo);

		//Nombre y ayuda
		$corto = $this->acortar_nombre($nodo->get_nombre_corto());
		$id = $nodo->get_id();
		$largo = $nodo->get_nombre_largo();
		$extra = $nodo->get_info_extra();

		if($this->_mostrar_ayuda && ($largo || $id || $extra)) {
			$title= "<b>Nombre</b>: $largo<br /><b>Id</b>:  $id";
			if ($extra != '') {
				$title .= "<hr />$extra";
			}
			$ayuda = toba_recurso::ayuda(null,  $title, 'ei-arbol-nombre');
			if (get_class($nodo) == 'toba_ci_pantalla_info') {
				$nombre= "<span $ayuda>$id</span>";
			} else {
				$nombre= "<span $ayuda>$corto</span>";
			}
		} else {
			$nombre = $corto;
		}
		
		if ($this->_mostrar_propiedades_nodos && $nodo->tiene_propiedades()) {
			$salida .= "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"".$nodo->get_id()."\");' ".
						"class='ei-arbol-ver-prop'>$nombre</a>";			
		} else {
			$salida .= $nombre;
		}
		return $salida;
	}
}