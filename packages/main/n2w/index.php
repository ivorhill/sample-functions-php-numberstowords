<?php

function main(array $args): array
{
    $id = $args['id'];

    $data = ["payload" => ["parcel" => [
        ["title" => "Parcel ID", "value" => "027370-0080", "columnno" => 1, "classNameTitle" => "lg-title"],
        ["title" => "Jurisdiction", "value" => "KENT", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Name", "value" => "IH3 PROPERTY BORROWER LP", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Levy Code", "value" => "_", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Address", "value" => "730 MAPLEWOOD AVE KENT 98030", "columnno" => 1, "classNameTitle" => "lg-title"],
        ["title" => "Property Type", "value" => "R (Deprecated Field)", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Residential Area", "value" => "Southwest", "columnno" => 1, "classNameTitle" => "lg-title"],
        ["title" => "Plat Block / Building #", "value" => "2", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Property Name", "value" => "_", "columnno" => 1, "classNameTitle" => "lg-title"],
        ["title" => "Plat Lot", "value" => "7", "columnno" => 2, "classNameTitle" => "lg-title"],
        ["title" => "Quarter-Section-Township-Range", "value" => "SE-19-22-05", "columnno" => 1, "classNameTitle" => "lg-title"],
        ["title" => "Legal Description", "value" => "ARMSCREST # 2 LESS S 13.5 FT", "columnno" => 1, "classNameTitle" => "lg-title"]
    ]]];

    $delay = rand(200, 1000);
    usleep($delay * 1000);

    return ["body" => json_encode($data)];
}
