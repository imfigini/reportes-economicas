
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	'Comisión Evaluadora', --nombre
	NULL, --nivel_acceso
	'Comisión Evaluadora', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3720'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3721'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3883'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3884'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3886'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3889'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3890'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'Reportes', --proyecto
	'com_evaluadora', --usuario_grupo_acc
	NULL, --item_id
	'3891'  --item
);
--- FIN Grupo de desarrollo 0
