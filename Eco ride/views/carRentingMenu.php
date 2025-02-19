<section class="car-renting__wrapper">
    <div class="container">

        <h1 class="car-renting__text">
            Adoptez <span class="big-word">EcoRide</span> : des trajets durables pour un avenir <span
                class="big-word">éco-responsable</span> !

        </h1>
        <div class="car-rental-menu">
            <form class="car-rental-form">
                <!-- Départ -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="depart" class="label">Départ</label>
                    <input type="text" id="depart" placeholder="Enter départ location" class="form-control">
                    <span class="line"></span>
                </div>

                <!-- Destination -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="destination" class="label">Destination</label>
                    <input type="text" id="destination" placeholder="Enter destination" class="form-control">
                    <span class="line"></span>
                </div>

                <!-- Aujourd'hui (Date Picker) -->
                <div class="form-group">
                    <i class="fa-solid fa-calendar-days icon"></i>
                    <label for="date" class="label">Aujourd'hui</label>
                    <input type="date" id="date" class="form-control">
                    <span class="line"></span>
                </div>

                <!-- Passengers -->
                <div class="form-group">
                    <i class="fa-solid fa-user icon"></i>
                    <label for="passenger" class="label">Passenger</label>
                    <select id="passenger" class="form-control">
                        <option value="1">1 Passenger</option>
                        <option value="2">2 Passengers</option>
                        <option value="3">3 Passengers</option>
                        <option value="4">4 Passengers</option>
                        <option value="5">5 Passengers</option>
                        <option value="6">6+ Passengers</option>
                    </select>

                </div>
                <!-- Search Button -->
                <div class="form-group">
                    <a class="search-btn">Search</a>
                </div>
            </form>
        </div>

    </div>
</section>
</header>
