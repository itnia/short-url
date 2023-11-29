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
                    <input type="text" name="long_url">
                    <input type="text" name="short_url">
                    <button type="submit">Создать</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        (function() {
            const form = document.getElementById('form-create-short-url');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const myFormData = new FormData(e.target);
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
                        }

                        if (data.status == 'exists_long_url') {
                            // -- логика обработки - такой url есть
                        }

                        if (data.status == 'exists_short_url') {
                            // -- логика обработки - такой короткий url есть
                        }

                        if (data.status == 'created') {
                            // -- логика обработки - короткий url создан
                        }
                    })
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

        .app-short-url__ {}
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>