<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only customer
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/schedulediet.css">

<body class="bodyt">
    <div class="content">
        <h1>📝 Schedule & Diet Planner</h1>
        
        <div class="bmi-calculator">
            <h2>BMI Calculator</h2>
            <form id="bmiForm">
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" id="weight" required>
                </div>
                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <input type="number" id="height" required>
                </div>
                <button type="submit" class="btn primary-btn">Calculate BMI</button>
            </form>
        </div>

        <div class="bmi-result" id="bmiResult" style="display:none;">
            <h2>Your BMI: <span id="bmiValue"></span></h2>
            <p>Status: <span id="bmiStatus"></span></p>

            <h3>Suggested Workout Plan</h3>
            <div id="workoutPlans" class="plans-grid"></div>

            <h3>Suggested Diet Plan</h3>
            <div id="dietPlans" class="plans-grid"></div>
        </div>
    </div>
</body>
<script>
const workoutOptions = {
    "underweight": [
        "Strength Training - 3x/week",
        "Full Body Circuit - 2x/week",
        "Protein-rich Gym Routine",
        "Resistance Bands Workout",
        "High-Calorie Weight Gain Program",
        "Compound Lifts Focus",
        "Push/Pull Split",
        "Leg Day Emphasis",
        "Powerlifting Intro",
        "Core & Stability Routine"
    ],
    "normal": [
        "Balanced Cardio & Strength",
        "HIIT 2x/week",
        "Moderate Weight Training",
        "Flexibility & Mobility",
        "Full Body Circuits",
        "Endurance Cardio",
        "Core & Stability",
        "Yoga/Stretching",
        "Functional Training",
        "Active Lifestyle Plan"
    ],
    "overweight": [
        "Cardio 5x/week",
        "HIIT Fat Loss Routine",
        "Bodyweight Strength",
        "Circuit Training",
        "Low-Calorie Resistance Training",
        "Walking & Jogging Plan",
        "Full Body Fat Burn",
        "Core & Stability Focus",
        "Mobility & Stretching",
        "Light Dumbbell Workouts"
    ]
};

const dietOptions = {
    "underweight": [
        "High Protein Diet",
        "Carb-Rich Meals",
        "Healthy Fats",
        "Frequent Small Meals",
        "Protein Shakes",
        "Lean Meat + Veggies",
        "Nuts & Seeds",
        "Whole Grain Pasta",
        "Smoothies & Juices",
        "Calorie Dense Snacks"
    ],
    "normal": [
        "Balanced Diet",
        "Moderate Protein & Carbs",
        "Vegetables & Fruits",
        "Low Sugar Intake",
        "Healthy Fats",
        "Whole Grains",
        "Lean Meat/Fish",
        "Dairy in Moderation",
        "Hydration Focus",
        "Meal Prep Strategy"
    ],
    "overweight": [
        "Low Calorie Diet",
        "High Protein, Low Carb",
        "Vegetable & Salad Focus",
        "Avoid Sugary Drinks",
        "Intermittent Fasting Option",
        "Lean Protein Choices",
        "Portion Control",
        "Healthy Snacks Only",
        "Drink More Water",
        "No Junk Foods"
    ]
};

document.getElementById('bmiForm').addEventListener('submit', function(e){
    e.preventDefault();
    
    const weight = parseFloat(document.getElementById('weight').value);
    const heightCm = parseFloat(document.getElementById('height').value);
    const heightM = heightCm / 100;

    let bmi = (weight / (heightM * heightM)).toFixed(1);
    let status = "";

    if(bmi < 18.5){
        status = "underweight";
    } else if(bmi >= 18.5 && bmi < 25){
        status = "normal";
    } else {
        status = "overweight";
    }

    document.getElementById('bmiValue').textContent = bmi;
    document.getElementById('bmiStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);

    // Populate workout plans
    const workoutDiv = document.getElementById('workoutPlans');
    workoutDiv.innerHTML = "";
    workoutOptions[status].forEach(plan=>{
        workoutDiv.innerHTML += `<div class="plan-card">${plan}</div>`;
    });

    // Populate diet plans
    const dietDiv = document.getElementById('dietPlans');
    dietDiv.innerHTML = "";
    dietOptions[status].forEach(plan=>{
        dietDiv.innerHTML += `<div class="plan-card">${plan}</div>`;
    });

    document.getElementById('bmiResult').style.display = 'block';
});
</script>
<?php
// Fetch plans from DB
$bmi = ['underweight','normal','overweight'];
$workoutPlans = [];
$dietPlans = [];

foreach($bmi as $cat){
    $workoutPlans[$cat] = [];
    $res = $conn->query("SELECT name FROM workout_plans WHERE category='$cat' ORDER BY name LIMIT 10");
    while($row=$res->fetch_assoc()) $workoutPlans[$cat][] = $row['name'];

    $dietPlans[$cat] = [];
    $res = $conn->query("SELECT name FROM diet_plans WHERE category='$cat' ORDER BY name LIMIT 10");
    while($row=$res->fetch_assoc()) $dietPlans[$cat][] = $row['name'];
}

// Pass these arrays to JS
?>

<script>
const workoutOptions = <?php echo json_encode($workoutPlans); ?>;
const dietOptions = <?php echo json_encode($dietPlans); ?>;
</script>

