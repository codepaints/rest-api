<?php


class Product
{
    // database connection and table name
    private $conn;
    private $table_name = 'products';

    // object properties
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $category_name;
    public $created;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * read products
     *
     * @return mixed
     */
    public function read()
    {

        // select all query
        $query = "SELECT c.name AS category_name,
                       p.id,
                       p.name,
                       p.description,
                       p.price,
                       p.category_id,
                       p.created
                FROM {$this->table_name} p
                         LEFT JOIN categories c on p.category_id = c.id
                ORDER BY p.created DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;

    }

    /**
     * @return bool
     */
    public function create()
    {

        // query to insert record
        $query = "INSERT INTO {$this->table_name} SET name=:name, price=:price, description=:description, category_id=:category_id, created=:created";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->name        = htmlspecialchars(strip_tags($this->name));
        $this->price       = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->created     = htmlspecialchars(strip_tags($this->created));

        //bind value
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':created', $this->created);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readOne()
    {

        // query to read single record
        $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
                    FROM products p
                             LEFT JOIN categories c on p.category_id = c.id
                    WHERE p.id = ?
                    LIMIT 0, 1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be update
        $stmt->bindParam(1, $this->id);

        //execute query
        $stmt->execute();

        // get retrived row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->name          = $row['name'];
        $this->price         = $row['price'];
        $this->description   = $row['description'];
        $this->category_id   = $row['category_id'];
        $this->category_name = $row['category_name'];

    }

    public function update()
    {
        // update query
        $query = "UPDATE {$this->table_name} 
                    SET 
                    name = :name, 
                    price = :price,
                    description = :description, 
                    category_id = :category_id
                    WHERE id = :id";

        //prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name        = htmlspecialchars(strip_tags($this->name));
        $this->price       = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->id          = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;

    }

    public function delete()
    {

        // delete query
        $query = "DELETE FROM {$this->table_name} WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind id of record to delte
        $stmt->bindParam(1, $this->id);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;

    }

}