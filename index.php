<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Задание 4</title>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
</head>
<body>
    <header>
        <h1>Задание №4</h1>
    </header>

    <div class="main">
        <h2>Пожалуйста, введите свои данные для регистрации</h2>
        
        <?php
        // Отображение ошибок из cookies
        if (isset($_COOKIE['form_errors'])) {
            $errors = json_decode($_COOKIE['form_errors'], true);
            echo '<div class="error-messages"><ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul></div>';
            
            // Удаляем cookie с ошибками после отображения
            setcookie('form_errors', '', time() - 3600, '/');
        }
        ?>
        
        <form method="post" action="process.php">
        
            <label>ФИО</label><br>
            <input type="text" name="full_name" required maxlength="150" 
                   placeholder="Иванов Иван Иванович"
                   value="<?php echo htmlspecialchars($_COOKIE['full_name'] ?? ''); ?>"
                   class="<?php echo isset($_COOKIE['field_errors']['full_name']) ? 'error-field' : ''; ?>"><br>
            <?php if (isset($_COOKIE['field_errors']['full_name'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['full_name']; ?></span><br>
            <?php endif; ?>

            
            <label>Номер телефона</label><br>
            <input type="tel" name="phone" required 
                   placeholder="+7 (XXX) XXX-XX-XX"
                   value="<?php echo htmlspecialchars($_COOKIE['phone'] ?? ''); ?>"
                   class="<?php echo isset($_COOKIE['field_errors']['phone']) ? 'error-field' : ''; ?>"><br>
            <?php if (isset($_COOKIE['field_errors']['phone'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['phone']; ?></span><br>
            <?php endif; ?>

    
            <label>Адрес электронной почты</label><br>
            <input type="email" name="email" required 
                   placeholder="example@mail.com"
                   value="<?php echo htmlspecialchars($_COOKIE['email'] ?? ''); ?>"
                   class="<?php echo isset($_COOKIE['field_errors']['email']) ? 'error-field' : ''; ?>"><br>
            <?php if (isset($_COOKIE['field_errors']['email'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['email']; ?></span><br>
            <?php endif; ?>

            
            <label>Дата рождения</label><br>
            <input type="date" name="birth_date" required
                   value="<?php echo htmlspecialchars($_COOKIE['birth_date'] ?? ''); ?>"
                   class="<?php echo isset($_COOKIE['field_errors']['birth_date']) ? 'error-field' : ''; ?>"><br>
            <?php if (isset($_COOKIE['field_errors']['birth_date'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['birth_date']; ?></span><br>
            <?php endif; ?>

            
            <label>Пол</label><br>
            <input type="radio" name="gender" value="male" required
                   <?php echo (isset($_COOKIE['gender']) && $_COOKIE['gender'] == 'male') ? 'checked' : ''; ?>> Мужской <br>
            <input type="radio" name="gender" value="female"
                   <?php echo (isset($_COOKIE['gender']) && $_COOKIE['gender'] == 'female') ? 'checked' : ''; ?>> Женский <br>
            <?php if (isset($_COOKIE['field_errors']['gender'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['gender']; ?></span><br>
            <?php endif; ?>

            
            <label>Любимый язык программирования</label><br>
            <select name="languages[]" size="5" multiple required
                    class="<?php echo isset($_COOKIE['field_errors']['languages']) ? 'error-field' : ''; ?>">
                <?php
                $options = [
                    '1' => 'Pascal',
                    '2' => 'C',
                    '3' => 'C++',
                    '4' => 'JavaScript',
                    '5' => 'PHP',
                    '6' => 'Python',
                    '7' => 'Java',
                    '8' => 'Haskel',
                    '9' => 'Clojure',
                    '10' => 'Prolog',
                    '11' => 'Scala'
                ];
                
                $selectedLangs = isset($_COOKIE['languages']) ? json_decode($_COOKIE['languages'], true) : [];
                
                foreach ($options as $value => $label) {
                    $selected = in_array($value, $selectedLangs) ? 'selected' : '';
                    echo "<option value=\"$value\" $selected>$label</option>";
                }
                ?>
            </select><br>
            <?php if (isset($_COOKIE['field_errors']['languages'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['languages']; ?></span><br>
            <?php endif; ?>

           
            <label>Биография</label><br>
            <textarea name="bio" rows="5"
                      class="<?php echo isset($_COOKIE['field_errors']['bio']) ? 'error-field' : ''; ?>"><?php 
                echo htmlspecialchars($_COOKIE['bio'] ?? ''); 
            ?></textarea><br>
            <?php if (isset($_COOKIE['field_errors']['bio'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['bio']; ?></span><br>
            <?php endif; ?>

          
            <input type="checkbox" name="contract" required
                   <?php echo isset($_COOKIE['contract']) ? 'checked' : ''; ?>> 
            <label>С контрактом ознакомлен(а)*</label><br>
            <?php if (isset($_COOKIE['field_errors']['contract'])): ?>
                <span class="field-error"><?php echo $_COOKIE['field_errors']['contract']; ?></span><br>
            <?php endif; ?>

            
            <button type="submit">Сохранить</button>
        </form>
    </div>

    <footer>
        <p>Баринова А.В. 27/2</p>
    </footer>
</body>
</html>