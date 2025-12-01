<h2>История бронирований</h2>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Место</th>
            <th>Рейс</th>
            <th>От</th>
            <th>До</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= $res->seat_number ?></td>
                <td><?= $res->timetable->id ?></td>
                <td><?= $res->startStopPoint->name ?></td>
                <td><?= $res->endStopPoint->name ?></td>
                <td><?= $res->timetable->date_start ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>