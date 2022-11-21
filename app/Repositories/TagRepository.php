<?php

namespace App\Repositories;

use App\Models\Tag;
use Exception;
use Illuminate\Support\Facades\DB;

class TagRepository
{
    public function getValidationRules(): array
    {
        return [
            'name' => 'required|max:255',
            'color' => 'required|max:6'
        ];
    }

    public function getById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    /**
     * @param int $spaceId
     * @param array $search
     * @return array
     */
    public function getMostExpensiveTags(int $spaceId, array $search = [])
    {
        $limit = array_key_exists("limit", $search) ? $search["limit"] : null;
        $year = array_key_exists("year", $search) ? $search["year"] : null;
        $month = array_key_exists("month", $search) ? $search["month"] : null;
        $sql = '
            SELECT
                tags.name AS name,
                tags.color AS color,
                SUM(transactions.amount) AS amount
            FROM
                tags
            LEFT OUTER JOIN
                transactions ON tags.id = transactions.tag_id AND transactions.deleted_at IS NULL
            WHERE
                tags.space_id = ?';

        if ($year) {
            $sql .= ' AND YEAR(happened_on) = ?';
        }

        if ($month) {
            $sql .= ' AND MONTH(happened_on) = ?';
        }

        $sql .= '
            GROUP BY
                tags.id
            ORDER BY
                SUM(transactions.amount) DESC
        ';

        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        $data = [$spaceId];

        if ($year) {
            $data[] = $year;
        }

        if ($month) {
            $data[] = $month;
        }

        return DB::select($sql . ';', $data);
    }

    public function create(int $spaceId, string $name, string $color): Tag
    {
        // Check if color is HEX
        if (strlen($color) !== 6 || !ctype_xdigit($color)) {
            throw new Exception('Invalid color');
        }

        return Tag::create([
            'space_id' => $spaceId,
            'name' => $name,
            'color' => $color
        ]);
    }

    public function update(int $tagId, array $data): void
    {
        $tag = Tag::find($tagId);

        if (!$tag) {
            throw new Exception('Could not find tag with ID ' . $tagId);
        }

        $tag->fill($data)->save();
    }
}
