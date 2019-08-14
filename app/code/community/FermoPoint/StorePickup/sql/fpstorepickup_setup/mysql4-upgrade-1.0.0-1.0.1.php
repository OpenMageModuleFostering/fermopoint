<?php

$installer = $this;

$installer->startSetup();

$collection = Mage::getModel('directory/region')->getResourceCollection()
    ->addCountryFilter('ITA')
    ->load()
;

if ( ! $collection->getSize())
{
    $data = array(
        array('IT', 'AG', 'Agrigento', 'Agrigento'),
        array('IT', 'AL', 'Alessandria', 'Alessandria'),
        array('IT', 'AN', 'Ancona', 'Ancona'),
        array('IT', 'AO', 'Aosta', 'Aosta'),
        array('IT', 'AR', 'Arezzo', 'Arezzo'),
        array('IT', 'AP', 'Ascoli Piceno', 'Ascoli Piceno'),
        array('IT', 'AT', 'Asti', 'Asti'),
        array('IT', 'AV', 'Avellino', 'Avellino'),
        array('IT', 'BA', 'Bari', 'Bari'),
        array('IT', 'BT', 'Barletta-Andria-Trani', 'Barletta-Andria-Trani'),
        array('IT', 'BL', 'Belluno', 'Belluno'),
        array('IT', 'BN', 'Benevento', 'Benevento'),
        array('IT', 'BG', 'Bergamo', 'Bergamo'),
        array('IT', 'BI', 'Biella', 'Biella'),
        array('IT', 'BO', 'Bologna', 'Bologna'),
        array('IT', 'BZ', 'Bolzano', 'Bolzano'),
        array('IT', 'BS', 'Brescia', 'Brescia'),
        array('IT', 'BR', 'Brindisi', 'Brindisi'),
        array('IT', 'CA', 'Cagliari', 'Cagliari'),
        array('IT', 'CL', 'Caltanissetta', 'Caltanissetta'),
        array('IT', 'CB', 'Campobasso', 'Campobasso'),
        array('IT', 'CI', 'Carbonia-Iglesias', 'Carbonia-Iglesias'),
        array('IT', 'CE', 'Caserta', 'Caserta'),
        array('IT', 'CT', 'Catania', 'Catania'),
        array('IT', 'CZ', 'Catanzaro', 'Catanzaro'),
        array('IT', 'CH', 'Chieti', 'Chieti'),
        array('IT', 'CO', 'Como', 'Como'),
        array('IT', 'CS', 'Cosenza', 'Cosenza'),
        array('IT', 'CR', 'Cremona', 'Cremona'),
        array('IT', 'KR', 'Crotone', 'Crotone'),
        array('IT', 'CN', 'Cuneo', 'Cuneo'),
        array('IT', 'EN', 'Enna', 'Enna'),
        array('IT', 'FM', 'Fermo', 'Fermo'),
        array('IT', 'FE', 'Ferrara', 'Ferrara'),
        array('IT', 'FI', 'Florence', 'Firenze'),
        array('IT', 'FG', 'Foggia', 'Foggia'),
        array('IT', 'FC', 'Forlì-Cesena', 'Forlì-Cesena'),
        array('IT', 'FR', 'Frosinone', 'Frosinone'),
        array('IT', 'GE', 'Genoa', 'Genova'),
        array('IT', 'GO', 'Gorizia', 'Gorizia'),
        array('IT', 'GR', 'Grosseto', 'Grosseto'),
        array('IT', 'IM', 'Imperia', 'Imperia'),
        array('IT', 'IS', 'Isernia', 'Isernia'),
        array('IT', 'SP', 'La Spezia', 'La Spezia'),
        array('IT', 'AQ', 'L\'Aquila', 'L\'Aquila'),
        array('IT', 'LT', 'Latina', 'Latina'),
        array('IT', 'LE', 'Lecce', 'Lecce'),
        array('IT', 'LC', 'Lecco', 'Lecco'),
        array('IT', 'LI', 'Livorno', 'Livorno'),
        array('IT', 'LO', 'Lodi', 'Lodi'),
        array('IT', 'LU', 'Lucca', 'Lucca'),
        array('IT', 'MC', 'Macerata', 'Macerata'),
        array('IT', 'MN', 'Mantua', 'Mantova'),
        array('IT', 'MS', 'Massa and Carrara', 'Massa e Carrara'),
        array('IT', 'MT', 'Matera', 'Matera'),
        array('IT', 'VS', 'Medio Campidano', 'Medio Campidano'),
        array('IT', 'ME', 'Messina', 'Messina'),
        array('IT', 'MI', 'Milan', 'Milano'),
        array('IT', 'MO', 'Modena', 'Modena'),
        array('IT', 'MB', 'Monza and Brianza', 'Monza e Brianza'),
        array('IT', 'NA', 'Naples', 'Napoli'),
        array('IT', 'NO', 'Novara', 'Novara'),
        array('IT', 'NU', 'Nuoro', 'Nuoro'),
        array('IT', 'OG', 'Ogliastra', 'Ogliastra'),
        array('IT', 'OT', 'Olbia-Tempio', 'Olbia-Tempio'),
        array('IT', 'OR', 'Oristano', 'Oristano'),
        array('IT', 'PD', 'Padua', 'Padova'),
        array('IT', 'PA', 'Palermo', 'Palermo'),
        array('IT', 'PR', 'Parma', 'Parma'),
        array('IT', 'PV', 'Pavia', 'Pavia'),
        array('IT', 'PG', 'Perugia', 'Perugia'),
        array('IT', 'PU', 'Pesaro and Urbino', 'Pesaro e Urbino'),
        array('IT', 'PE', 'Pescara', 'Pescara'),
        array('IT', 'PC', 'Piacenza', 'Piacenza'),
        array('IT', 'PI', 'Pisa', 'Pisa'),
        array('IT', 'PT', 'Pistoia', 'Pistoia'),
        array('IT', 'PN', 'Pordenone', 'Pordenone'),
        array('IT', 'PZ', 'Potenza', 'Potenza'),
        array('IT', 'PO', 'Prato', 'Prato'),
        array('IT', 'RG', 'Ragusa', 'Ragusa'),
        array('IT', 'RA', 'Ravenna', 'Ravenna'),
        array('IT', 'RC', 'Reggio Calabria', 'Reggio Calabria'),
        array('IT', 'RE', 'Reggio Emilia', 'Reggio Emilia'),
        array('IT', 'RI', 'Rieti', 'Rieti'),
        array('IT', 'RN', 'Rimini', 'Rimini'),
        array('IT', 'RM', 'Rome', 'Roma'),
        array('IT', 'RO', 'Rovigo', 'Rovigo'),
        array('IT', 'SA', 'Salerno', 'Salerno'),
        array('IT', 'SS', 'Sassari', 'Sassari'),
        array('IT', 'SV', 'Savona', 'Savona'),
        array('IT', 'SI', 'Siena', 'Siena'),
        array('IT', 'SO', 'Sondrio', 'Sondrio'),
        array('IT', 'SR', 'Syracuse', 'Siracusa'),
        array('IT', 'TA', 'Taranto', 'Taranto'),
        array('IT', 'TE', 'Teramo', 'Teramo'),
        array('IT', 'TR', 'Terni', 'Terni'),
        array('IT', 'TO', 'Turin', 'Torino'),
        array('IT', 'TP', 'Trapani', 'Trapani'),
        array('IT', 'TN', 'Trento', 'Trento'),
        array('IT', 'TV', 'Treviso', 'Treviso'),
        array('IT', 'TS', 'Trieste', 'Trieste'),
        array('IT', 'UD', 'Udine', 'Udine'),
        array('IT', 'VA', 'Varese', 'Varese'),
        array('IT', 'VE', 'Venice', 'Venezia'),
        array('IT', 'VB', 'Verbano-Cusio-Ossola', 'Verbano-Cusio-Ossola'),
        array('IT', 'VC', 'Vercelli', 'Vercelli'),
        array('IT', 'VR', 'Verona', 'Verona'),
        array('IT', 'VV', 'Vibo Valentia', 'Vibo Valentia'),
        array('IT', 'VI', 'Vicenza', 'Vicenza'),
        array('IT', 'VT', 'Viterbo', 'Viterbo'),
    );

    foreach ($data as $row) 
    {
        $bind = array(
            'country_id'    => $row[0],
            'code'          => $row[1],
            'default_name'  => $row[2],
        );
        $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
        $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));
        
        $bind = array(
            'locale'    => 'it_IT',
            'region_id' => $regionId,
            'name'      => $row[3],
        );
        $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
    }
}

$installer->endSetup(); 
