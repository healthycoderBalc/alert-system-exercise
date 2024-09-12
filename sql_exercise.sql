SELECT
    c.ID,
    c.Nombre,
    c.Apellido,
    SUM(v.Importe) AS Total_Importe
FROM
    Clientes c
JOIN
    Ventas v ON c.ID = v.Id_cliente
WHERE
    v.Fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY
    c.ID, c.Nombre, c.Apellido
HAVING
    SUM(v.Importe) > 100000;
