import 'dart:convert';
import 'package:app/models/relief_center.dart';
import 'package:app/models/request.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class RequestApi {
  final String baseUrl = 'http://localhost:8000/api';
  Future<Map<String, dynamic>> getUserRequests() async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('login_token');
    final response = await http.get(
      Uri.parse('$baseUrl/requests/user'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    if (response.statusCode == 200) {
      final decoded = json.decode(response.body);

      final List<dynamic> centerData = decoded['center_data'];
      List<ReliefCenter> parsedCenters = centerData.map((json) => ReliefCenter.fromJson(json)).toList();
      final List<dynamic> data = decoded['data'];
      return {
        'requests': data.map((json) => Request.fromJson(json)).toList(),
        'parsedCenters': parsedCenters,
      };  
    } else {
      throw Exception('Failed to load requests');
    }
  }

  Future<void> deleteRequest(String id) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final response = await http.delete(
      Uri.parse('$baseUrl/requests/$id'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    if (response.statusCode != 200 && response.statusCode != 204) {
      throw Exception('Failed to delete contribution');
    }
  }

  Future<Request> addRequest(Request request) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    
    final response = await http.post(
      Uri.parse('$baseUrl/add_requests/user'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode(request.toJson()),
    );

    if (response.statusCode == 201) {
      return Request.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to add request. Status: ${response.statusCode}');
    }
  }

  // Update an existing request (PUT)
  Future<Request> updateRequest(Request request) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    
    final response = await http.put(
      Uri.parse('$baseUrl/requests/user/${request.id}'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode(request.toJson()),
    );
    
    if (response.statusCode == 200) {
      return Request.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to update request. Status: ${response.statusCode}');
    }
  }
}
