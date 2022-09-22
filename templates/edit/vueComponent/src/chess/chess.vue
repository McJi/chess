<template>
	<div id="chess">
		<div class="p__swiper chess-slider _nav-offset _pagination-top _pagination-hide-nav" :class="{'_ready': ready}"
			title=" ">
			<div class="chess-slider__inner">
				<div class="floors-list _left">
					<div class="floors-list__floor" v-for="floorIndex in arSectionFloors">
						<span class="foors-list__floor-number">{{floorIndex}}</span>
						<span class="foors-list__floor-text">{{lang.CITRUS_DEVELOPER_CHESS_FLOOR}}</span>
					</div>
				</div>
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<div class="swiper-slide" v-for="(section, sectionIndex) in sections" v-if="isEdit || section.flats.length">
							<div class="chess-section" :id="section.editId">
								<div class="chess-section__name">
									<div class="chess-section__name-text">{{ section.name }}</div>

									<div v-if="isEdit" class="chess-section__edit-links">
										<a href="javascript:void(0);" :title="lang.CHESS_CLONE_SECTION_LINK_TITLE"
											@click="cloneSection(sectionIndex)" class="chess__section-clone-link">
											<svg class="svg-icon" viewBox="0 0 511.627 511.627">
												<use xlink:href="#icon-copy" />
											</svg>
										</a>
										<a href="javascript:void(0);" :title="lang.CHESS_CLONE_SECTION_DELETE_TITLE"
											@click="deleteSection(sectionIndex)" class="chess__section-clone-link">
											<svg class="svg-icon _trash" viewBox="0 0 774.266 774.266">
												<use xlink:href="#icon-trash" />
											</svg>
										</a>

									</div>
								</div>

								<div class="chess-floor" v-for="floorIndex in arSectionFloors" :class="{
								   '_empty': floorIndex > section.floorsCount,
								   '_edit': isEdit,
								}">
									<template v-if="section.floorsCount">
										<div class="chess__floor-edit-area" v-if="isEdit">
											<a href="javascript:void(0);" class="chess__floor-edit-link"
												@click="cloneFloor(sectionIndex, floorIndex)">
												<svg class="svg-icon chess__floor-edit-link__icon" viewBox="0 0 511.627 511.627">
													<use xlink:href="#icon-copy" />
												</svg>

												<span class="chess__floor-edit-link__title">{{lang.CHESS_CLONE_FLOOR_LINK}}</span>
											</a>
											<a href="javascript:void(0);" class="chess__floor-edit-link _delete"
												@click="deleteFloorFlats(sectionIndex, floorIndex)">
												<svg class="svg-icon chess__floor-edit-link__icon icon-trash" viewBox="0 0 774.266 774.266">
													<use xlink:href="#icon-trash" />
												</svg>
												<span class="chess__floor-edit-link__title">{{lang.CHESS_CLONE_FLOOR_DELETE}}</span>
											</a>
										</div>
										<div :class="['chess-flat',
												{ '_disable': !flat.sale },
												{ '_selected': !flat.active },
										    { '_new': !flat.id },
										    { '_first': !flatIndex },
										    { '_last': getSortedFlats(sectionIndex, floorIndex).length === flatIndex+1 }
										]" v-for="(flat, flatIndex) in getSortedFlats(sectionIndex, floorIndex)"
											ref="flats" :id="flat.editId">
											{{ flat.rooms }}<template
												v-if="(+flat.rooms && flat.rooms == +flat.rooms)">{{lang.CHESS_K}}</template>

											<a v-if="flatIndex" @click="changeSortFlat(sectionIndex, floorIndex, flatIndex, -1)"
												class="chess-flat__sort-left" href="javascript:void(0);"></a>
											<a @click="changeSortFlat(sectionIndex, floorIndex, flatIndex, 1)" class="chess-flat__sort-right"
												v-if="flatIndex+1 < getSortedFlats(sectionIndex, floorIndex).length"
												href="javascript:void(0);"></a>
											<a :title="lang.CHESS_DELETE_FLAT_TITLE" @click.prevent="deleteFlat(sectionIndex, flat)"
												href="javascript:void(0);" class="chess-flat__delete-flat">
												<svg class="svg-icon" viewBox="0 0 357 357">
													<use xlink:href="#icon-close" />
												</svg>
											</a>
										</div>
									</template>

									<div class="chess-flat _add-flat-block" v-if="isEdit && (floorIndex <= section.floorsCount ||
									(!section.floorsCount && floorIndex === 1))">
										<a @click="openAddFlatForm(sectionIndex, floorIndex);" href="javascript:void(0);"
											class="add-flat-link">
											<svg class="svg-icon" viewBox="0 0 24 24">
												<use xlink:href="#icon-plus" />
											</svg>
										</a>
									</div>
								</div><!-- .chess-floor -->
							</div><!-- .chess-section -->
						</div><!-- .swiper-slide -->

						<a @click.prevent="addSection()" href="javascript:void(0);" class="swiper-slide add-section-slide"
							v-if="isEdit">
							<span class="add-section-link__icon">
								<svg class="svg-icon" viewBox="0 0 24 24">
									<use xlink:href="#icon-plus" />
								</svg>
							</span>
							<span class="add-section-link__text">{{lang.CHESS_ADD_SECTION_LINK}}</span>
						</a>
					</div><!-- .swiper-wrapper -->
				</div><!-- .swiper-container -->

				<div class="floors-list _right">
					<div class="floors-list__floor" v-for="floorIndex in arSectionFloors">
						<span class="foors-list__floor-number">{{floorIndex}}</span>
						<span class="foors-list__floor-text">{{lang.CITRUS_DEVELOPER_CHESS_FLOOR}}</span>
					</div>
				</div>
			</div><!-- .chess-swiper__inner -->

			<div class="swiper-pagination swiper-pagination--lines"></div>

			<div class="swiper-button-prev"><span class="icon-arrow_left"></span></div>
			<div class="swiper-button-next"><span class="icon-arrow_right"></span></div>
		</div><!-- .chess -->

		<div class="chess-data-form" v-if="isEdit">
			<input name="chess-edit" type="hidden" v-model="postData">
			<input name="chess-delete" type="hidden" v-model="postDeleteData">
			<a href="javascript:void(0);" @click="resetChess()">{{lang.CHESS_DATA_FORM_CANCEL_BUTTN}}</a>
		</div>

		<div class="chess__forms" v-if="isEdit">
			<div ref="flat-popup" class="modal-content chess__popup">
				<div class="modal-header">
					<div class="modal-title">{{lang.CHESS_ADD_FLAT_FORM_TITLE}}</div>
					<a href="javascript:void(0);" @click.prevent="closePopup()" class="modal-close-btn" data-dismiss="modal">
						<svg class="svg-icon" viewBox="0 0 357 357">
							<use xlink:href="#icon-close" />
						</svg>
						<span class="icon-close"></span>
					</a>
				</div>
				<div class="modal-body">
					<form ref="flat-popup-form" action="" class="chess-edit-form" v-if="properties">
						<div class="chess-edit-form__field" :data-type="field.type" :data-code="fieldCode"
							v-for="(field, fieldCode) in properties">
							<div class="chess-edit-form__field-title">{{field.name}}: </div>
							<div class="chess-edit-form__field-values">
								<template v-if="field.type === 'list'">
									<label class="field-plan" v-for="(fieldItem, fieldIndex) in field.items">
										<input class="field-plan__input" :name="fieldCode" type="radio" :value="fieldItem.id"
											v-model="forms.flat.values[fieldCode]">
										<span class="field-plan__image-wrapper">
											<span :style="{'background-image': 'url('+fieldItem.image+')'}" class="field-plan__image"></span>
										</span>
										<span class="field-plan__name">{{fieldItem.name}}</span>
									</label>
								</template>
								<template v-else-if="field.type === 'bool'">
									<label class="field-checkbox square-checkbox">
										<input type="checkbox" :name="fieldCode" class="square-checkbox__input" value="Y"
											v-model="forms.flat.values[fieldCode]"> <span class="square-checkbox__checkmark"></span>
										<span class="field-checkbox__label">{{lang.CHESS_FORM_BOOL_LABEL}}</span>
									</label>
									<label class="field-checkbox square-checkbox">
										<input type="checkbox" :name="fieldCode" class="square-checkbox__input" value="N"
											v-model="forms.flat.values[fieldCode]"> <span class="square-checkbox__checkmark"></span>
										<span class="field-checkbox__label">{{lang.CHESS_FORM_BOOL_LABEL}}</span>
									</label>
								</template>
							</div>
						</div>

						<div class="chess-edit-form__footer">
							<a @click="closePopup()" href="javascript:void(0);"
								class="chess-btn _secondary">{{lang.CHESS_FORM_CANCEL_BUTTON}}</a>
							<button @click.prevent="addFlat()" type="submit"
								class="chess-btn _primary">{{lang.CHESS_FORM_SUBMIT_BUTTON}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script src="./script.js"></script>