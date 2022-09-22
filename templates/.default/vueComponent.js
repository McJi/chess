
;(function(){
	Vue.component('vue-chess', {
		template: "#chess-template",
		props: ['items','selectedPlans', 'properties'],
		data: function(){
			return {
				isShowHover: false,
				hoveredFlat: {},
				activeTippy: {},
				isMobile: BX.browser.IsMobile(),
				swiper: {},
			}
		},
		computed: {
			maxSectionFloor: function () {
				var arFloorCount = this.items.reduce(function(arPreviousValue, section){
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
		},
		methods: {
			isSelectedPlan: function (flat) {
				return flat.active && (this.selectedPlans.indexOf(flat.plan) + 1);
			},
			updateSlider: function (fn) {
				var fn = fn || function () {};
				this.$nextTick(function () {
					this.swiper.update();
					fn.call(this);
				});
			},
			getFlats: function(sectionIndex, floorIndex){
				return this.items[sectionIndex]['flats'].filter(function (flat) {
					return +flat.floor === +floorIndex;
				});
			},
		},
		created: function () {
		
		},
		mounted: function () {
			var self = this;
			if (!this.isEdit && this.$refs.flats) {
				
				this.$refs.flats.forEach(function (item) {
					var templateId = item.dataset.tippyTemplate;
					if (templateId) {
						tippy(item, {
							html: '#'+templateId,
							placement: window.innerWidth >= 768 ? 'right' : 'bottom',
							arrow: true,
							theme: 'template-color',
							duration: 0,
							//delay: [0, 100000], //for debug
							performance: true,
							trigger: self.isMobile ? 'click' : 'mouseenter focus',
							interactive: true,
							zIndex: 9,
							offset: '8,12',
							onShow: function() {
								self.activeTippy = this._reference._tippy;
							},
							onHide: function () {
								self.activeTippy = {};
							}
						});
					}
				});
			}
			
			BX.addCustomEvent('plan-filter-add', function (planId) {
				self.selectedPlans.push(+planId);
			});
			BX.addCustomEvent('plan-filter-remove', function (planId) {
				self.$delete(self.selectedPlans, self.selectedPlans.indexOf(+planId));
			});
			
			// http://idangero.us/swiper/api/
			this.swiper = new Swiper('.chess-slider .swiper-container', {
				watchOverflow: true,
				
				// pagination
				pagination: {
					el: '.chess-slider .swiper-pagination',
					clickable: true,
				},
				
				simulateTouch: false,
				
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
						if(self.activeTippy.hide) self.activeTippy.hide();
					}
				}
			});
		}
	});
}());