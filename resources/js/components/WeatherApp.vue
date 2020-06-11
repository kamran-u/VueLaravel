<template>
    <div class="text-white mb-8">
        <div class="places-input text-gray-800">
            <input type="text" class="w-full px-2 py-2" v-model="queryString" @keyup="searchLocation" placeholder="Start by typing first 3 letters of any UK city...">
            <ul class="mb-8">
              <li v-for="(city, index) in cities[0]" @click="fetchData(city.city_id, city.city_name)">{{ city.city_name }}</li>
            
            </ul>
        </div>
        <div class="weather-container font-sans w-128 max-width-lg overflow-hidden bg-gray-900 shadow-lg mt-4 rounded-lg">
            <div class="current-weather flex items-center justify-between px-6 py-8">
                <div class="flex items-center">
                    <div>
                        <div class="text-6xl font-semibold">{{currentTemp.actual}}&degC</div>
                        <div>Feels like {{currentTemp.feels}}&degC</div>
                        <div>{{currentTemp.time | moment("ddd, MMMM Do YYYY")}}</div>
                    </div>
                </div>
                <div class="mx-5">
                    <div class="font-semibold">{{currentTemp.main}} - {{currentTemp.summary}}</div>
                    <div>{{location.city}}, {{location.country}}</div>
                </div>
                <div><img :src="iconLink(currentTemp.icon)"></div>
            </div>

            <div class="future-weather text-sm bg-gray-800 px-6 py-2 overflow-hidden" v-for="(day, index) in forecast" :key="index" >
                <div class="flex items-center mt-8">
                    <div class="w-2/6 text-sm text-gray-200">{{ day.dt_txt | moment("ddd Do, h:mm a") }}</div>      
                    <div class="w-3/6 px-4 flex items-center">
                        <div><img :src="iconLink(day.weather[0].icon)"></div>
                        <div class="ml-3">{{ day.weather[0].main }} - {{ day.weather[0].description }}</div>
                    </div>      
                    <div class="w-1/6 text-right">
                        <div>{{ Math.ceil(day.main.temp_max) }}&degC</div>
                        <div>{{ Math.floor(day.main.temp_min) }}&degC</div>
                    </div>      
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
            this.fetchData();
        },

        data() {
            return{

                queryString: '',
                cities: [],
                cityId: 2643123,

                currentTemp: {
                    time: '',
                    actual: '',
                    feels: '',
                    main:'',
                    summary: '',
                    icon: '',
                    humidity: ''
                },

                location: {
                    name: '',
                    country: '',
                    lat: '',
                    lng: ''
                },

                forecast: ''

            }
        },
        methods: {

            fetchData(cityId, cityName){
                let self = this;

                if(typeof cityId != 'undefined'){
                    self.cityId = cityId;
                    self.queryString = cityName;
                    self.cities = [];
                }

                fetch(`/api/weather?cityid=${self.cityId}`)
                .then(response => response.json())
                .then(data => {

                    self.location.city = data.city.name;
                    self.location.country = data.city.country;

                    self.currentTemp.time = data.list[0].dt_txt;
                    self.currentTemp.actual= Math.round(data.list[0].main.temp);
                    self.currentTemp.feels= Math.round(data.list[0].main.feels_like);
                    self.currentTemp.humidity= data.list[0].main.humidity;
                    self.currentTemp.main= data.list[0].weather[0].main;
                    self.currentTemp.summary= data.list[0].weather[0].description;
                    self.currentTemp.icon= data.list[0].weather[0].icon;

                    self.forecast = data.list;

                })
            },

            searchLocation(){
                let self = this;
                self.cities = [];
                if(self.queryString.length < 3) return false;
                fetch(`/api/city?query=${self.queryString}`)
                .then(response => response.json())
                .then(data => {
                    self.cities.push(data);
                });
            },

            iconLink(icon) {
                return `http://openweathermap.org/img/w/${icon}.png`
            }
        }
    }
</script>
<style scoped>
ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
  position:absolute;
}

ul li {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  cursor:pointer;
}
</style>
