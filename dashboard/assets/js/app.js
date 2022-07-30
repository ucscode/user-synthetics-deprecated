'use strict';

/* ===== Enable Bootstrap Popover (on element  ====== */

var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})

/* ==== Enable Bootstrap Alert ====== */
var alertList = document.querySelectorAll('.alert')
alertList.forEach(function (alert) {
  new bootstrap.Alert(alert)
});


/* ===== Responsive Sidepanel ====== */
const sidePanelToggler = document.getElementById('sidepanel-toggler'); 
const sidePanel = document.getElementById('app-sidepanel');  
const sidePanelDrop = document.getElementById('sidepanel-drop'); 
const sidePanelClose = document.getElementById('sidepanel-close'); 

if( sidePanelToggler ) {
	
	window.addEventListener('load', function(){
		responsiveSidePanel(); 
	});

	window.addEventListener('resize', function(){
		responsiveSidePanel(); 
	});


	function responsiveSidePanel() {
		let w = window.innerWidth;
		if(w >= 1200) {
			// if larger 
			//console.log('larger');
			sidePanel.classList.remove('sidepanel-hidden');
			sidePanel.classList.add('sidepanel-visible');
			
		} else {
			// if smaller
			//console.log('smaller');
			sidePanel.classList.remove('sidepanel-visible');
			sidePanel.classList.add('sidepanel-hidden');
		}
	};

	sidePanelToggler.addEventListener('click', () => {
		if (sidePanel.classList.contains('sidepanel-visible')) {
			console.log('visible');
			sidePanel.classList.remove('sidepanel-visible');
			sidePanel.classList.add('sidepanel-hidden');
			
		} else {
			console.log('hidden');
			sidePanel.classList.remove('sidepanel-hidden');
			sidePanel.classList.add('sidepanel-visible');
		}
	});



	sidePanelClose.addEventListener('click', (e) => {
		e.preventDefault();
		sidePanelToggler.click();
	});

	sidePanelDrop.addEventListener('click', (e) => {
		sidePanelToggler.click();
	});

};


/* ====== Mobile search ======= */
const searchMobileTrigger = document.querySelector('.search-mobile-trigger');
const searchBox = document.querySelector('.app-search-box');

if( searchMobileTrigger ) {
	
	searchMobileTrigger.addEventListener('click', () => {

		searchBox.classList.toggle('is-visible');
		
		let searchMobileTriggerIcon = document.querySelector('.search-mobile-trigger-icon');
		
		if(searchMobileTriggerIcon.classList.contains('fa-search')) {
			searchMobileTriggerIcon.classList.remove('fa-search');
			searchMobileTriggerIcon.classList.add('fa-times');
		} else {
			searchMobileTriggerIcon.classList.remove('fa-times');
			searchMobileTriggerIcon.classList.add('fa-search');
		}
		
	});

};


// --- [ chart & bars ] ---

var chartColors = {
	green: '#75c181',
	gray: '#a9b5c9',
	text: '#252930',
	border: '#e7e9ed'
};


// --- [ Configuration for barChart ] ---
 
const barChartConfig = {
	type: 'bar',
	data: {
		/* 
			-- [ add your labels here ] --
			labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] 
		*/
		datasets: [{
			/* label: 'Members', */
			backgroundColor: window.chartColors.green,
			borderColor: window.chartColors.green,
			borderWidth: 1,
			maxBarThickness: 30
			/*
				-- [ add your data here ] --!
				data: [
					23,
					45,
					76,
					75,
					62,
					37,
					83
				]
			*/
		}]
	},
	options: {
		responsive: true,
		aspectRatio: 1.5,
		legend: {
			position: 'bottom',
			align: 'end',
		},
		title: {
			display: true,
			text: 'Bar Chart' //  -- [ add your text here ] --
		},
		tooltips: {
			mode: 'index',
			intersect: false,
			titleMarginBottom: 10,
			bodySpacing: 10,
			xPadding: 16,
			yPadding: 16,
			borderColor: window.chartColors.border,
			borderWidth: 1,
			backgroundColor: '#fff',
			bodyFontColor: window.chartColors.text,
			titleFontColor: window.chartColors.text,

		},
		scales: {
			xAxes: [{
				display: true,
				gridLines: {
					drawBorder: false,
					color: window.chartColors.border,
				},

			}],
			yAxes: [{
				display: true,
				gridLines: {
					drawBorder: false,
					color: window.chartColors.borders,
				},

				
			}]
		}
		
	}
}


// --- [ a function to simplify the use of barChart ] ---

function execBarChart( Obj ) {
	
	var canvas = $(Obj.el).get(0);
	if( !canvas || canvas.tagName != 'CANVAS' ) 
		throw Error("Element is not a type canvas");
	
	var config = uss.meth.JSONParser(JSON.stringify(barChartConfig));
	if( !config ) throw Error("Bar Chart Misconfigured");
	
	config.options.title.text = (Obj.name) ? Obj.name : Obj.text;
	config.data.labels = Obj.labels;
	config.data.datasets[0].data = Obj.data;
	config.data.datasets[0].label = Obj.hoverText;
	
	var canvas2D = canvas.getContext('2d');
	var chart = new Chart(canvas2D, config);
	
	return chart;
	
}


// --- [ show active dropdown ] ---

$(function() {
	if( $(".submenu-link.active").length ) {
		$( $(".submenu-link.active").get(0) )
			.parents("[data-bs-parent]").addClass('show')
			.prev().addClass('active');
	};
});