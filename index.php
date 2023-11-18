<?php
$filename = "вар1.csv";
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$data = [];
foreach ($lines as $key => $line) {
    if ($key == 0) {
        continue;
    }
    $data[] = str_getcsv($line, ";");
}

$types = array_unique(array_column($data, 0));
$brands = array_unique(array_column($data, 1));

$vehicles = [];
foreach ($data as $row) {
    $vehicles[$row[0]][] = $row[1];
}

$types = array_keys($vehicles);
?>

<form action="index.php" method="post">
    <select name="vehicle_type" id="vehicle_type">
        <?php foreach ($types as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="vehicle_brand" id="vehicle_brand">
        <?php foreach ($brands as $brand): ?>
            <option value="<?= htmlspecialchars($brand) ?>"><?= htmlspecialchars($brand) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="distance" placeholder="Введите расстояние в км" id="distance">

    <p>Расход бензина на 100 км: <span id="fuel_consumption"></span></p>

    <button id="count">Рассчитать</button>
</form>

<script>
    const calculateFuelConsumption = (e) => {
        e.preventDefault()
        const vehicleType = document.getElementById('vehicle_type').value;
        const vehicleBrand = document.getElementById('vehicle_brand').value;
        const distance = document.getElementById('distance').value;

        if (!distance) {
            document.getElementById('fuel_consumption').innerText = 'Введите расстояние';
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'getFuelCons.php?vehicle_type=' + encodeURIComponent(vehicleType) + '&vehicle_brand=' + encodeURIComponent(vehicleBrand) + '&distance=' + encodeURIComponent(distance), true);
        xhr.onreadystatechange =  () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('fuel_consumption').innerText = xhr.responseText + ' л.';
            }
        };
        xhr.send();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('count').addEventListener('click', calculateFuelConsumption)
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const vehicleBrandSelect = document.getElementById('vehicle_brand');
    
    const updateBrands = () => {
        const selectedType = vehicleTypeSelect.value;
        const brands = <?php echo json_encode($vehicles); ?>;
        
        vehicleBrandSelect.innerHTML = ''; // Очищаем текущие опции
        
        if (brands[selectedType]) {
            brands[selectedType].forEach(function(brand) {
                const option = new Option(brand, brand);
                vehicleBrandSelect.add(option);
            });
        }
    }
    
    // Обработчик события изменения типа автомобиля
    vehicleTypeSelect.addEventListener('change', updateBrands);

    // Начальное обновление марок при загрузке
    updateBrands();
    });
</script>