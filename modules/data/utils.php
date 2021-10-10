<?php

function translateFilterCondition($filtercondition){
	
	switch ($filtercondition)
				{
			case "CONTAINS":
				return "LIKE";
				break;
			case "DOES_NOT_CONTAIN":
				return "NOT LIKE";
				break;
			case "EQUAL":
				return "=";
				break;
			case "NOT_EQUAL":
				return "<>";
				break;
			case "GREATER_THAN":
				return ">";
				break;
			case "LESS_THAN":
				return "<";
				break;
			case "GREATER_THAN_OR_EQUAL":
				return ">=";
				break;
			case "LESS_THAN_OR_EQUAL":
				return "<=";
				break;
			case "NULL":
				return "IS NULL";
				break;
			case "NOT_NULL":
				return "IS NOT NULL";
				break;
			case "IN":
				return "IN";
				break;
			default: return "UNKNOWN";				
}
};

?>