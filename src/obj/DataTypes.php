<?php

namespace mangeld\obj;

class DataTypes
{
  const cardThreeColumns = '683b5e06-9ba1-425f-88bd-d3667b4cdc13';
  const cardForm = 'a8620342-f3e2-4b90-9f01-eb3b412db22d';
  const fieldEmail = 'd082a408-e024-4998-8484-3f0b3d4902af';
  const fieldText = '48fa0dfc-ecce-4adf-ab22-4dacb307e452';
  const fieldTitle = '2b870b7c-7d25-4366-a948-49b0b0fb512b';
  const fieldImage = 'c48f1022-60d3-4b51-9290-6605152b8a90';

  public static function typeName($type)
  {
    switch( $type )
    {
      case DataTypes::cardForm:
        return 'cardForm';
        break;
      case DataTypes::cardThreeColumns:
        return 'cardThreeColumns';
        break;
      case DataTypes::fieldEmail:
        return 'fieldEmail';
        break;
      case DataTypes::fieldText:
        return 'fieldText';
        break;
      case DataTypes::fieldTitle:
        return 'fieldTitle';
        break;
      case DataTypes::fieldImage:
        return 'fieldImage';
        break;

    }
  }
}
