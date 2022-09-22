export default {
	name: 'vue-chess',
	props: ['items', 'isEdit', 'properties', 'lang'],
	data: function () {
		return {
			sections: {},
			isShowHover: false,
			hoveredFlat: {},
			isMobile: BX.browser.IsMobile(),
			swiper: {},
			forms: {
				'flat': {
					'section': -1,
					'floor': -1,
					'values': {
					
					},
					'defaultValues': {
						"active": true
					}
				}
			},
			deleteData: {
				'sections': [],
				'flats': [],
			},
			ready: false,
		}
	},
	computed: {
		maxSectionFloor: function () {
			var arFloorCount = this.sections.reduce(function (arPreviousValue, section) {
				if (section.floorsCount) arPreviousValue.push(section.floorsCount);
				return arPreviousValue;
			}, []);
			return !arFloorCount.length ? 1 : Math.max.apply(null, arFloorCount);
		},
		arSectionFloors: function () {
			var floors = [];
			for (var i = this.maxSectionFloor; i; i--) {
				floors.push(i);
			}
			return floors;
		},
		postData: function () {
			let self = this,
				changedData = [],
				flatFields = ['id', 'floor', 'active', 'plan', 'rooms', 'sort'];
			this.sections.forEach(function (section) {
				let changedSection = self.cloneData(section);
				let changedFlats = changedSection.flats.filter((flat) => !flat.id || flat.changed);
				
				let clearChangedFlats = changedFlats.reduce((previewValue, flat) => {
					let newFlat = {};
					flatFields.forEach( fieldCode => newFlat[fieldCode] = flat[fieldCode]);
					previewValue.push(newFlat);
					return previewValue;
				}, []);
				
				if (clearChangedFlats.length || section.changed || !changedSection.id) {
					changedSection.flats = clearChangedFlats;
					changedData.push(changedSection);
				}
			});
			return JSON.stringify(changedData);
		},
		postDeleteData: function () {
			return JSON.stringify(this.deleteData);
		}
	},
	methods: {
		/**
		 * clone and delete reactive
		 */
		cloneData: function (data) {
			var self = this;
			var skipProperties = ['__ob__', 'reactiveGetter', 'reactiveSetter'];
//array
			if (Array.isArray(data)) {
				var cloneArray = [];
				data.forEach(function (dataArrayItem, dataArrayIndex) {
					if (skipProperties.indexOf(dataArrayIndex) > -1) return;
					cloneArray[dataArrayIndex] = self.cloneData(dataArrayItem);
				});
				return cloneArray;
			}
//object
			if (typeof data === 'object' && data.toString && data.toString() == "[object Object]") {
				var cloneObject = {};
				var dataObjectItemKey;
				for (dataObjectItemKey in data) {
					if (skipProperties.indexOf(dataObjectItemKey) > -1) continue;
					cloneObject[dataObjectItemKey] = this.cloneData(data[dataObjectItemKey]);
				}
				return cloneObject;
			}
			return data;
		},
		addFlat: function () {
			var vueForm = this.forms.flat,
				values = vueForm.values;
			
			var flat = {
				id: 0,
				rooms: 1,
				name: 'New flat',
				active: !!values.active,
				floor: vueForm.floor || 1,
			};
			
			if (values.layout) {
				var layout = {};
				this.properties.layout.items.forEach(function (layoutIt) {
					if (+layoutIt.id === +values.layout) layout = layoutIt;
				});
				if (layout.rooms) flat.rooms = layout.rooms;
				if (layout.id) flat.plan = +layout.id;
			}
			
			if (!this.sections[vueForm.section]['floorsCount'])
				this.sections[vueForm.section]['floorsCount'] = 1;
			
			if (!this.sections[vueForm.section]['flats'])
				this.sections[vueForm.section]['flats'] = [];
			
			// вначале обновим все сортировки у элементов
			flat.sort = this.updateFlatsSortIndex(vueForm.section, flat.floor);
			
			// добавляем индекс сортировки исходня из количества квартир на этаже
			
			this.sections[vueForm.section]['flats'].push(flat);
			this.updateSlider();
			
			this.forms.flat.values = this.cloneData(this.forms.flat.defaultValues);
			$.magnificPopup.close();
		},
		openAddFlatForm: function (sectionIndex, floorIndex) {
			this.forms.flat.section = sectionIndex;
			this.forms.flat.floor = floorIndex;
			$.magnificPopup.open({
				items: {
					src: this.$refs['flat-popup'],
				},
				mainClass: 'full-screen-map mfp-with-zoom',
				alignTop: false,
				showCloseBtn: false,
				closeOnContentClick: false,
			});
		},
		closePopup: function () {
			$.magnificPopup.close();
			this.forms.flat.values = {};
		},
		addSection: function (newSection) {
			var newSection = newSection || {
					floorsCount: 1,
					flats: [],
				},
				nextSectionCount = this.sections.length + 1;
			
			newSection.name = this.lang.CHESS_SECTION_TITLE + ' ' + (nextSectionCount);
			newSection.id = 0;
				
			this.sections.push(newSection);
			this.updateSlider(function () {
				this.swiper.slideTo(nextSectionCount);
			});
		},
		deleteSection: function (sectionIndex) {
			let section = this.sections[sectionIndex];
			let deletedFlatsId = section['flats'].reduce(function (arDelete, flat) {
				if(flat.id) arDelete.push(flat.id);
				return arDelete;
			}, []);
			this.deleteData.flats = this.deleteData.flats.concat(deletedFlatsId);
			
			if(section.id)
				this.deleteData.sections.push(section.id);
			
			this.$delete(this.sections, sectionIndex);
			this.updateSlider();
		},
		cloneFloor: function (sectionIndex, floorIndex) {
			var section = this.sections[sectionIndex],
				flats = this.cloneData(this.getFlats(sectionIndex, floorIndex));
			
			section.floorsCount++;
			
			flats = flats.map(function (flat) {
				flat.id = 0;
				flat.editId = 0;
				flat.floor = section.floorsCount;
				return flat;
			});
			section.flats = section.flats.concat(flats);
		},
		cloneSection: function (sectionIndex) {
			var newSection = this.cloneData(this.sections[sectionIndex]);
			newSection.flats = newSection.flats.map(function (flat) {
				flat.id = 0;
				flat.editId = 0;
				return flat;
			});
			this.addSection(newSection);
		},
		resetChess: function () {
			this.sections = this.cloneData(this.items);
			this.deleteData.sections = [];
			this.deleteData.flats = [];
			this.updateSlider();
		},
		updateSlider: function (fn) {
			var fn = fn || function () {
			};
			this.$nextTick(function () {
				this.swiper.update();
				fn.call(this);
			});
		},
		
		/**
		 * Для квартир созданных не через шахматку
		 * обновление сортировок у квартир исходя из позиции в массиве
		 * по формуле index*10
		 * @return (int) возвращает сортировку для нового элемента
 		 */
		updateFlatsSortIndex: function(sectionIndex, floorIndex){
			let floorFlats = this.getSortedFlats(sectionIndex, floorIndex);
			floorFlats.forEach((flat, index) => {
				let newSort = (index+1)*10;
				if (flat.sort !== newSort)
					flat.changed = true;
				flat.sort = newSort;
			});
			return (floorFlats.length+1)*10;
		},
		
		getSortedFlats: function (sectionIndex, floorIndex) {
			return this.getFlats(sectionIndex, floorIndex).sort((a,b) => {
				var sortResult = 0;
				if (a.sort !== b.sort) {
					sortResult = a.sort > b.sort ? 1 : -1;
				} else if (a.rooms !== b.rooms) {
					sortResult = a.sort > b.sort ? 1 : -1;
				}
				return sortResult;
			});
		},
		getFlats: function (sectionIndex, floorIndex) {
			return this.sections[sectionIndex]['flats'].filter(function (flat) {
				return +flat.floor === +floorIndex;
			});
		},
		deleteFloorFlats: function (sectionIndex, floorIndex) {
			var section = this.sections[sectionIndex];
			section.changed = true;
			var deletedFlatsId = section['flats'].reduce(function (arDelete, flat) {
				if (flat.floor === floorIndex && flat.id) arDelete.push(flat.id);
				return arDelete;
			}, []);
			this.deleteData.flats = this.deleteData.flats.concat(deletedFlatsId);
			
			var flats = section['flats'].filter(function (flat) {
				return flat.floor !== floorIndex;
			});
			flats = flats.map(function (flat) {
				if (flat.floor > floorIndex) {
					flat.changed = true;
					flat.floor--;
				}
				return flat;
			});
			section['flats'] = flats;
			section.floorsCount--;
		},
		deleteFlat: function (sectionIndex, flat) {
			if (flat.id) this.deleteData.flats.push(flat.id);
			var flatIndex = this.sections[sectionIndex]['flats'].indexOf(flat);
			this.$delete(this.sections[sectionIndex]['flats'], flatIndex);
		},
		changeSortFlat: function(sectionIndex, floorIndex, flatIndex, changePosition) {
			let floorFlats = this.getSortedFlats(sectionIndex, floorIndex);
			
			this.updateFlatsSortIndex(sectionIndex, floorIndex);
			
			let flat = floorFlats[flatIndex];

            flat.changed = true;
			if (changePosition > 0) {
				flat.sort += 10;
				floorFlats[flatIndex + 1]['sort'] -= 10;
                floorFlats[flatIndex + 1]['changed'] = true;
			} else {
				flat.sort -= 10;
				floorFlats[flatIndex - 1]['sort'] += 10;
                floorFlats[flatIndex - 1]['changed'] = true;
			}
		}
	},
	created: function () {
		this.sections = this.cloneData(this.items);
		if (this.properties.layout && this.properties.layout.items && this.properties.layout.items[0])
			this.forms.flat.defaultValues.layout = this.properties.layout.items[0]['id'];
	},
	mounted: function () {
		var self = this;
		
		this.forms.flat.values = this.cloneData(this.forms.flat.defaultValues);
		
		var visibleInterval = setInterval(function () {
			if (self.$el.offsetParent !== null) {
				self.swiper.update();
				self.ready = true;
				clearInterval(visibleInterval);
			}
		}, 100);

// http://idangero.us/swiper/api/
		this.swiper = new Swiper('.chess-slider .swiper-container', {
			watchOverflow: true,
			
			simulateTouch: true,
			
			freeMode: true,

// Navigation arrows
			navigation: {
				nextEl: '.chess-slider .swiper-button-next',
				prevEl: '.chess-slider .swiper-button-prev',
			},
			
			slidesPerView: 'auto',
			spaceBetween: 40,
			breakpoints: {
				767: {
					spaceBetween: 20
				},
				1023: {
					spaceBetween: 30
				}
			},
			on: {
				'touchStart': function () {
				
				}
			}
		});
	}
}