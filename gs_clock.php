<?php
/*
Plugin Name: Analog Clock Widget
Description: Fixed-position analog clock widget for GetSimple CE with customizable settings.
Version: 1.1
Author: Zeth
*/

// Register plugin
register_plugin(
    'gs_clock',
    'Analog Clock',
    '1.1',
    'Zeth',
    'https://getsimple-ce.ovh/',
    'Analog Clock Widget for GetSimple CE',
    'plugins',
    'gs_clock_settings_page'
);

// Add backend settings tab under Plugins or Settings
add_action('plugins-sidebar', 'createSideMenu', array('gs_clock', 'Clock Settings'));

// Save settings handler
function gs_clock_settings_page() {
    // Default fallback values
    $default_settings = array(
        'opacity' => '1.0',
        'pos_top' => 'false', 
        'pos_bottom' => 'true', 
        'pos_left' => 'false', 
        'pos_right' => 'true', 
        'pos_middle' => 'false',
        'clock_size' => '140', 
        'numbers_size' => '13', 
        'hour_hand' => '38', 
        'minute_hand' => '52', 
        'second_hand' => '58',
        'bg_color' => '#001144', 
        'num_color' => '#ffffff', 
        'hour_color' => '#ffffff', 
        'min_color' => '#ffffff', 
        'sec_color' => '#ff4757'
    );

    if (isset($_POST['submit_gs_clock'])) {
        $settings = array();
        $settings['opacity'] = isset($_POST['opacity']) && trim($_POST['opacity']) !== '' ? trim($_POST['opacity']) : $default_settings['opacity'];
        $settings['pos_top'] = isset($_POST['pos_top']) ? 'true' : 'false';
        $settings['pos_bottom'] = isset($_POST['pos_bottom']) ? 'true' : 'false';
        $settings['pos_left'] = isset($_POST['pos_left']) ? 'true' : 'false';
        $settings['pos_right'] = isset($_POST['pos_right']) ? 'true' : 'false';
        $settings['pos_middle'] = isset($_POST['pos_middle']) ? 'true' : 'false';

        // Check text fields and default if empty
        $fields = array('clock_size', 'numbers_size', 'hour_hand', 'minute_hand', 'second_hand', 'bg_color', 'num_color', 'hour_color', 'min_color', 'sec_color');
        
        foreach ($fields as $field) {
            $is_set = isset($_POST[$field]); $is_not_empty = false;
            if ($is_set) {
                if (trim($_POST[$field]) !== '') {
                    $is_not_empty = true;
                }
            }

            if ($is_set) {
                if ($is_not_empty) {
                    $settings[$field] = trim($_POST[$field]);
                } else {
                    $settings[$field] = $default_settings[$field];
                }
            } else {
                $settings[$field] = $default_settings[$field];
            }
        }
        
        // Save to JSON file in data other path
        file_put_contents(GSDATAOTHERPATH . 'gs_clock.json', json_encode($settings));
        
        echo '<p class="updated">Settings saved successfully!</p>';
    }

    // Load saved settings
    $saved_settings = array();
    if (file_exists(GSDATAOTHERPATH . 'gs_clock.json')) {
        $saved_settings = json_decode(file_get_contents(GSDATAOTHERPATH . 'gs_clock.json'), true);
        if (!is_array($saved_settings)) {
            $saved_settings = array();
        }
    }

    // Merge saved settings with default fallbacks
    $settings = array_merge($default_settings, $saved_settings);

    // Final fallback check for empty values
    foreach ($default_settings as $k => $v) {
        $not_set = !isset($settings[$k]);
        if ($not_set) {
            $settings[$k] = $v;
        } else {
            $is_empty = (trim($settings[$k]) === '');
            if ($is_empty) {
                $settings[$k] = $v;
            }
        }
    }
    ?>
    <style>
        .gs-clock-admin-wrap {
            font-size: 14px;
            max-width: 100%;
            overflow-x: hidden;
        }
        .gs-clock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 15px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .gs-clock-admin-wrap input[type="text"] {
            width: 100% !important;
            box-sizing: border-box;
            font-size: 14px !important;
            padding: 6px 8px !important;
        }
        .gs-checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        @media screen and (max-width: 768px) {
            .gs-clock-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .gs-checkbox-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <div class="gs-clock-admin-wrap">
        <h3><strong>Analog Clock Widget</strong></h3>
       
        <p>Fixed-position analog clock widget for GetSimple CE</p>
        <p style="border-left: 3px solid #ff0000; padding-left: 10px; margin: 10px 0;">
            To display the widget, add the following template tag to your template's body section:<br>
            <strong>Template Tag: <code>&lt;?php get_gs_clock(); ?&gt;</code></strong>
        </p>

        <form action="" method="post">
            <h3>Clock Widget Settings</h3>
            
            <p style="margin-bottom:5px;"><strong>Opacity:</strong></p>
            <div style="margin-bottom: 20px; max-width: 200px;">
                <input type="text" name="opacity" value="<?php echo htmlspecialchars($settings['opacity']); ?>"> <small>(e.g., 0.5 50% transparent)</small>
            </div>

            <p style="margin-bottom:10px;"><strong>Clock Position:</strong></p>
            <div class="gs-checkbox-group">
                <label style="display: inline-flex; align-items: center; gap: 5px;"><input type="checkbox" name="pos_top" <?php if($settings['pos_top']=='true') echo 'checked'; ?>> Top</label>
                <label style="display: inline-flex; align-items: center; gap: 5px;"><input type="checkbox" name="pos_bottom" <?php if($settings['pos_bottom']=='true') echo 'checked'; ?>> Bottom</label>
                <label style="display: inline-flex; align-items: center; gap: 5px;"><input type="checkbox" name="pos_left" <?php if($settings['pos_left']=='true') echo 'checked'; ?>> Left</label>
                <label style="display: inline-flex; align-items: center; gap: 5px;"><input type="checkbox" name="pos_right" <?php if($settings['pos_right']=='true') echo 'checked'; ?>> Right</label>
                <label style="display: inline-flex; align-items: center; gap: 5px;"><input type="checkbox" name="pos_middle" <?php if($settings['pos_middle']=='true') echo 'checked'; ?>> Center</label>
            </div>

            <p style="margin-bottom:0;"><strong>Size Settings (px):</strong></p>
            <div class="gs-clock-grid">
                <div>Clock<br><input type="text" name="clock_size" value="<?php echo htmlspecialchars($settings['clock_size']); ?>"></div>
                <div>Numbers<br><input type="text" name="numbers_size" value="<?php echo htmlspecialchars($settings['numbers_size']); ?>"></div>
                <div>Hour Hand<br><input type="text" name="hour_hand" value="<?php echo htmlspecialchars($settings['hour_hand']); ?>"></div>
                <div>Minute Hand<br><input type="text" name="minute_hand" value="<?php echo htmlspecialchars($settings['minute_hand']); ?>"></div>
                <div>Second Hand<br><input type="text" name="second_hand" value="<?php echo htmlspecialchars($settings['second_hand']); ?>"></div>
            </div>

            <p style="margin-bottom:0;"><strong>Color Settings:</strong></p>
            <div class="gs-clock-grid">
                <div>Background<br><input type="text" name="bg_color" value="<?php echo htmlspecialchars($settings['bg_color']); ?>"></div>
                <div>Numbers<br><input type="text" name="num_color" value="<?php echo htmlspecialchars($settings['num_color']); ?>"></div>
                <div>Hour Hand<br><input type="text" name="hour_color" value="<?php echo htmlspecialchars($settings['hour_color']); ?>"></div>
                <div>Minute Hand<br><input type="text" name="min_color" value="<?php echo htmlspecialchars($settings['min_color']); ?>"></div>
                <div>Second Hand<br><input type="text" name="sec_color" value="<?php echo htmlspecialchars($settings['sec_color']); ?>"></div>
            </div>

            <p>
                <input type="submit" name="submit_gs_clock" value="Save Settings" class="submit" style="background:#001144; color:#fff; padding:8px 15px; margin-top:20px; border:none; border-radius:4px; cursor:pointer;">
            </p>
        </form>
    </div>
    <?php
}

// Frontend template function to output the clock widget and script
function get_gs_clock() {
    $default_settings = array(
        'opacity' => '1.0',
        'pos_top' => 'false', 
        'pos_bottom' => 'true', 
        'pos_left' => 'false', 
        'pos_right' => 'true', 
        'pos_middle' => 'false',
        'clock_size' => '140', 
        'numbers_size' => '13', 
        'hour_hand' => '38', 
        'minute_hand' => '52', 
        'second_hand' => '58',
        'bg_color' => '#001144', 
        'num_color' => '#ffffff', 
        'hour_color' => '#ffffff', 
        'min_color' => '#ffffff', 
        'sec_color' => '#ff4757'
    );

    $saved_settings = array();
    if (file_exists(GSDATAOTHERPATH . 'gs_clock.json')) {
        $saved_settings = json_decode(file_get_contents(GSDATAOTHERPATH . 'gs_clock.json'), true);
        if (!is_array($saved_settings)) {
            $saved_settings = array();
        }
    }
    $settings = array_merge($default_settings, $saved_settings);

    foreach ($default_settings as $k => $v) {
        $not_set = !isset($settings[$k]);
        if ($not_set) {
            $settings[$k] = $v;
        } else {
            $is_empty = (trim($settings[$k]) === '');
            if ($is_empty) {
                $settings[$k] = $v;
            }
        }
    }

    // Compute dynamic layout/positioning styles based on explicit combinations
    $pos_css = "";
    $is_top = ($settings['pos_top'] === 'true');
    $is_bottom = ($settings['pos_bottom'] === 'true');
    $is_left = ($settings['pos_left'] === 'true');
    $is_right = ($settings['pos_right'] === 'true');
    $is_center = ($settings['pos_middle'] === 'true');

    if ($is_top && $is_center) {
        $pos_css = "top: 20px; left: 50%; transform: translateX(-50%); bottom: auto; right: auto;";
    } elseif ($is_bottom && $is_center) {
        $pos_css = "bottom: 20px; left: 50%; transform: translateX(-50%); top: auto; right: auto;";
    } elseif ($is_center) {
        $pos_css = "top: 50%; left: 50%; transform: translate(-50%, -50%); bottom: auto; right: auto;";
    } else {
        if ($is_top) { $pos_css .= "top: 20px; "; }
        if ($is_bottom) { $pos_css .= "bottom: 20px; "; }
        if ($is_left) { $pos_css .= "left: 20px; right: auto;"; } 
        elseif ($is_right) { $pos_css .= "right: 20px; left: auto;"; }
    }

    $c_size = intval($settings['clock_size']);
    $center_dot_size = max(6, round($c_size * 0.07));
    $opacity_val = floatval($settings['opacity']);
    ?>
    
    <style>
        .analog-clock-container-gs {
            position: fixed;
            <?php echo $pos_css; ?>
            z-index: 9999;
            margin: 0;
            opacity: <?php echo $opacity_val; ?>;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 3px;
            border-radius: 50%;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .analog-clock-container-gs .clock-face {
            width: <?php echo $c_size; ?>px;
            height: <?php echo $c_size; ?>px;
            background-color: <?php echo htmlspecialchars($settings['bg_color']); ?>;
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            position: relative;
        }
        .analog-clock-container-gs .number {
            position: absolute;
            width: 100%;
            height: 100%;
            text-align: center;
            transform: rotate(var(--rot));
        }
        .analog-clock-container-gs .number span {
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%) rotate(calc(-1 * var(--rot)));
            font-size: <?php echo intval($settings['numbers_size']); ?>px;
            font-weight: 700;
            color: <?php echo htmlspecialchars($settings['num_color']); ?>;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        }
        .analog-clock-container-gs .clock-face::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: <?php echo $center_dot_size; ?>px;
            height: <?php echo $center_dot_size; ?>px;
            background-color: <?php echo htmlspecialchars($settings['sec_color']); ?>;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.4);
        }
        .analog-clock-container-gs .hand {
            position: absolute;
            bottom: 50%;
            left: 50%;
            transform-origin: bottom center;
            border-radius: 4px;
        }
        .analog-clock-container-gs .hour-hand {
            width: 4px;
            height: <?php echo intval($settings['hour_hand']); ?>px;
            background-color: <?php echo htmlspecialchars($settings['hour_color']); ?>;
            transform: translateX(-50%) rotate(0deg);
            z-index: 3;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
        }
        .analog-clock-container-gs .minute-hand {
            width: 3px;
            height: <?php echo intval($settings['minute_hand']); ?>px;
            background-color: <?php echo htmlspecialchars($settings['min_color']); ?>;
            transform: translateX(-50%) rotate(0deg);
            z-index: 2;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
        }
        .analog-clock-container-gs .second-hand {
            width: 2px;
            height: <?php echo intval($settings['second_hand']); ?>px;
            background-color: <?php echo htmlspecialchars($settings['sec_color']); ?>;
            transform: translateX(-50%) rotate(0deg);
            z-index: 4;
        }
    </style>

    <div class="analog-clock-container-gs">
        <div class="clock-face">
            <div class="number" style="--rot: 30deg;"><span>1</span></div>
            <div class="number" style="--rot: 60deg;"><span>2</span></div>
            <div class="number" style="--rot: 90deg;"><span>3</span></div>
            <div class="number" style="--rot: 120deg;"><span>4</span></div>
            <div class="number" style="--rot: 150deg;"><span>5</span></div>
            <div class="number" style="--rot: 180deg;"><span>6</span></div>
            <div class="number" style="--rot: 210deg;"><span>7</span></div>
            <div class="number" style="--rot: 240deg;"><span>8</span></div>
            <div class="number" style="--rot: 270deg;"><span>9</span></div>
            <div class="number" style="--rot: 300deg;"><span>10</span></div>
            <div class="number" style="--rot: 330deg;"><span>11</span></div>
            <div class="number" style="--rot: 360deg;"><span>12</span></div>

            <div class="hand hour-hand" id="gsHourHand"></div>
            <div class="hand minute-hand" id="gsMinuteHand"></div>
            <div class="hand second-hand" id="gsSecondHand"></div>
        </div>
    </div>

    <script>
        (function() {
            const hourHand = document.getElementById('gsHourHand');
            const minuteHand = document.getElementById('gsMinuteHand');
            const secondHand = document.getElementById('gsSecondHand');

            function updateClock() {
                const now = new Date();
                const seconds = now.getSeconds();
                const minutes = now.getMinutes();
                const hours = now.getHours();

                const secondDegrees = (seconds / 60) * 360;
                const minuteDegrees = ((minutes + seconds / 60) / 60) * 360;
                const hourDegrees = ((hours % 12 + minutes / 60) / 12) * 360;

                if (secondHand) secondHand.style.transform = `translateX(-50%) rotate(${secondDegrees}deg)`;
                if (minuteHand) minuteHand.style.transform = `translateX(-50%) rotate(${minuteDegrees}deg)`;
                if (hourHand) hourHand.style.transform = `translateX(-50%) rotate(${hourDegrees}deg)`;
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
    <?php
}
