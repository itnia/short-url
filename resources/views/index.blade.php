<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShortUrl</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <main class="app-short-url">
        <div class="app-short-url__inner">
            <h1>Создание коротких ссылок</h1>

            <div class="app-short-url__form">
                <form id="form-create-short-url" action="">
                    <div class="mb-3">
                        <label for="inputLongUrl" class="form-label">Основная ссылка</label>
                        <input type="text" class="form-control" id="inputLongUrl" name="long_url">
                    </div>
                    <label for="binputShortUrl" class="form-label">Короткая ссылка</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon3">{{ url('/') }}/</span>
                        <input type="text" class="form-control" id="inputShortUrl" aria-describedby="basic-addon3" name="short_url">
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Создать</button>

                        <div style="display: contents;">
                            <button id="update" class="btn btn-primary" style="display: none;">Обновить</button>
                            <input type="text" hidden name="idShortUrl">
                        </div>
                    </div>

                    <div class="form-create-short-url__error" style="display: none;"></div>
                    <div class="form-create-short-url__responce" style="display: none;">
                        Ссылка создана: <a class="app-short-url__short-url" href=""></a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        (function() {
            const form = document.getElementById('form-create-short-url');

            form.querySelectorAll('input').forEach(function(element){
                element.addEventListener('change', function () {
                    form.querySelector('.form-create-short-url__error').style.display = "none";
                    form.querySelector('.form-create-short-url__responce').style.display = "none";
                    form.querySelector('.app-short-url__short-url').textContent = "";
                });
            });

            form.querySelector('[type="submit"]').addEventListener('click', function(e) {
                e.preventDefault();

                form.querySelector('.form-create-short-url__responce').style.display = "none";

                const myFormData = new FormData(form);
                const formDataObj = {};
                myFormData.forEach((value, key) => (formDataObj[key] = value));

                // -- реализация валидации
                // -- валидация url : коректность, длина, слэши, заполненость

                const data = {
                    long_url: formDataObj.long_url,
                    short_url: formDataObj.short_url
                }

                fetch('http://127.0.0.1:8000/api/short_url', {
                        method: 'POST',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                        },
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        console.log(data);

                        if (data.status == 'error') {
                            // -- логика обработки ошибок
                            form.querySelector('.form-create-short-url__error').style.display = "block";
                            form.querySelector('.form-create-short-url__error').textContent = data.message;
                            form.querySelector('.form-create-short-url__responce').style.display = "none";
                            
                            return;
                        }
                        
                        if (data.status == 'exists') {
                            form.querySelector('.form-create-short-url__error').style.display = "block";
                            form.querySelector('.form-create-short-url__error').textContent = data.message;
                            form.querySelector('.form-create-short-url__responce').style.display = "none";
                            
                            document.querySelector('[name="idShortUrl"]').value = data.id;
                            document.getElementById('update').style.display = "block";

                            return;
                        }

                        form.querySelector('.form-create-short-url__responce').style.display = "block";
                        const url = "{{ url('/') }}/" + data.short_url;
                        form.querySelector('.app-short-url__short-url').textContent = url;
                        form.querySelector('.app-short-url__short-url').href = url;
                    })
            });

            document.getElementById('update').addEventListener('click', function (e) {
                e.preventDefault();
                console.log(document.querySelector('[name="idShortUrl"]').value);

                // обновить ресурс по id
            });

        })();
    </script>

    <style>
        .app-short-url {
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #03A9F4;
        }

        .app-short-url__inner {
            background-color: white;
            box-shadow: 0 0 6px -2px black;
            border-radius: 1rem;
            padding: 1rem;
            position: relative;
            top: -25%;
        }

        .app-short-url__form {}
        
        .form-create-short-url {}

        .form-create-short-url__error {}

        .form-create-short-url__responce {}
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>