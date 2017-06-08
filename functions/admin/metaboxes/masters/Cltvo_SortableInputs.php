<?php

/**
 * Para usar este meta se requiere sobreescribir los siguientes métodos:
 * 	cltvoDisplayMetabox - La tabla a imprimir
 * 	modelRow - HTML, sirve como modelo del tr que se va aimprimir cada que se de click en el  botón de agregar
 *
 * 	Importante se require conservar las siguientes clases pues se usan en el JS:
 * 		1. .cltvo_sortable_table_JS - contenedor de la tabla
 * 		2. .#tbody__sortable_JS - tbody de la tabla que se despliega
 * 		3. .tr_sortable_JS - en ambas tablas, sirve para activar la funci'on de sortable
 * 		4. .#sortable_clone_JS - row (tr) de la tabla que se clona usa como modelo
 * 		5. .add__sortable_JS - botón de agregar
 * 		6. .delete__sortable_JS - botón de eliminar
 *
 * 		También se deben usar los siguientes atributos
 * 		data-meta-key="<?php echo $this->meta_key; ?>"  - en .delete__sortable_JS, .add__sortable_JS y se imprime 
 * 		data-key = "<?php echo $key; ?>" - en los inputs del modelo, el $key refiere a la llava del array de inputs
 * 		
 */		
class Cltvo_SortableInputs extends Cltvo_Metabox_Master {

	protected $description_metabox = 'Agrega y ordena:';
	
	protected $post_type = 'page';

	protected $inputs = array(
		'sucursal'	=> 'Sucursal',
		'telefono'	=> 'Teléfono',
		'link'		=> 'Link',
		'direccion'	=> 'Dirección'
	);

	/* Define el metodo que inicializa el valor del meta */
	 public static function setMetaValue($meta_value){
		 if (is_array($meta_value)) {
			foreach ($meta_value as $key => $value) {
				foreach ((new static)->inputs as $input_key => $item) {
					$meta_value[$key][$input_key] = (isset($meta_value[$key][$input_key]) && !empty($meta_value[$key][$input_key])) ? $meta_value[$key][$input_key] : '';
				}
			}
			return $meta_value;
		 } else {
		 	//si no existe, se incializa el primer elemento de array de meta_value
		 	//:: [$this->inputs] -> [[$inited_inputs]]
		 	return array(array_map(function($v) {return '';}, (new static)->inputs));
		 }
	 }


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	 public function CltvoDisplayMetabox($object){
		// echo '<pre>'; print_r($this->meta_value); echo '</pre>';
		?>
				
		<br>
		<div class="cltvo_sortable_table__container_JS">

			<table class="cltvo_sortable_table" style="width:100%;" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?php foreach ($this->inputs as $item): ?>
							<th class="th__cltvo_sortable_table" align="left" style="padding-bottom:10px;">
								<?php echo $item; ?>
							</th>
						<?php endforeach; ?>
						<th class="th__cltvo_sortable_table" style="padding-bottom:10px;">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody id="tbody__sortable_JS">
					<?php $this->drawTemplate($this->meta_value); ?>
				</tbody>
			</table>
			
			<button type="button" 
					class="button add__sucursal add__sortable_JS" 
					style="display:block;margin-top:0px;" 
					data-meta-key="<?php echo $this->meta_key; ?>">
					Agregar
					</button>

			<?php $this->modelRow(); ?>
		</div>
		
 		<?php 
 	}

 	public function modelRow() {//el row que JS usa como modelo para crear un nuevo row
 		?>
 		<table style="display:none;">
 			<tr id="sortable_clone_JS" class="tr_sortable tr_sortable_JS">
 				<?php foreach ($this->inputs as $key => $item): ?>
 					<td class="td__sucursal td__<?php echo $key; ?>" style="padding-bottom:10px;">
 						<?php if ( $key != 'direccion') : ?>
 							<input type="text" style="width: calc(100% - 10px);" disabled
 							id="<?php echo $this->meta_key; ?>__<?php echo $key; ?>"
 							name="<?php echo $this->meta_key; ?>[][<?php echo $key; ?>]"
 							data-key = "<?php echo $key; ?>"
 							class="input__sortable input__sortable_JS" />
 						<?php else : ?>
 							<textarea type="text" rows="3" disabled
 							id="<?php echo $this->meta_key; ?>__<?php echo $key; ?>"
 							name="<?php echo $this->meta_key; ?>[][<?php echo $key; ?>]"
 							data-key = "<?php echo $key; ?>"
 							class="input__sortable input__sortable_JS"></textarea>
 						<?php endif; ?>
 					</td>
 				<?php endforeach; ?>
 				<td class="td__sucursales delete__sucursal">
 					<button data-meta-key="<?php echo $this->meta_key; ?>" class="button delete__sortable_JS">&#10005;</button>
 					<br />
 					<button class="button move">&varr;</button>
 				</td>
 			</tr>
 		</table>
 		<?php
 	}

	public function drawTemplate($meta_value) {
		
		foreach ($meta_value as $key_value => $value) { ?>
			<tr id="<?php echo $this->meta_key; ?>_<?php echo $key_value; ?>" class="tr_sortable tr_sortable_JS">
				<?php foreach ($this->inputs as $key => $item): ?>
						<td class="td__sucursal td__<?php echo $key; ?>" style="padding-bottom:10px;">
							<?php if ( $key != 'direccion') : ?>
								<input type="text" style="width: calc(100% - 10px);"
								id="<?php echo $this->meta_key; ?>_<?php echo $key_value; ?>_<?php echo $key; ?>"
								name="<?php echo $this->meta_key; ?>[<?php echo $key_value; ?>][<?php echo $key; ?>]"
								value="<?php echo $value[$key]; ?>"
								class="input__sortable input__sortable_JS" />
							<?php else : ?>
								<textarea type="text" rows="3"
								id="<?php echo $this->meta_key; ?>_<?php echo $key_value; ?>_<?php echo $key; ?>"
								name="<?php echo $this->meta_key; ?>[<?php echo $key_value; ?>][<?php echo $key; ?>]"
								class="input__sortable input__sortable_JS"><?php echo $value[$key]; ?></textarea>
							<?php endif; ?>
						</td>
				<?php endforeach; ?>
					<td class="td__sucursales delete__sucursal">
						<button data-meta-key="<?php echo $this->meta_key; ?>" class="button delete__sortable_JS">&#10005;</button>
						<br />
						<button class="button move">&varr;</button>
					</td>
			</tr>
		<?php }

	}
 }