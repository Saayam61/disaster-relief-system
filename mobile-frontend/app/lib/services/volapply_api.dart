import 'dart:convert';
import 'package:app/models/volunteer.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class VolapplyApi {
  final String baseUrl = 'http://localhost:8000/api';

  Future<Volunteer> fetchCurrentVolunteer() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final response = await http.get(
      Uri.parse('$baseUrl/volunteer'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) {
      print(response.body);
      try {
        final decoded = json.decode(response.body);
        final volunteerJson = decoded[0][0]; 
        return Volunteer.fromJson(volunteerJson);
      } catch (e) {
        print(e.toString());
        throw Exception('Failed to parse volunteer data');
      }
    } else {
      throw Exception('Failed to load user');
    }
  }

  Future<void> updateVolunteer({
    required String skills,
    required String availability,
    required String status,
    }) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('login_token');
      final response = await http.post(
        Uri.parse('$baseUrl/updateVolunteer'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json'
        },
        body: json.encode({
          'skills': skills,
          'availability': availability,
          'status': status
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        print('Volunteer updated successfully!');
      }
    } catch (e) {
      throw Exception('Something went wrong while updating the volunteer');
    }
  }

  Future<void> applyCenter({required int userId}) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('login_token');
      final response = await http.post(
        Uri.parse('$baseUrl/applyCenter/$userId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json'
        },
        body: json.encode({
          'userId': userId,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        print('User applied successfully!');
      }
    } catch (e) {
      throw Exception('Something went wrong while applying ');
    }
  }

  Future<void> applyOrg({required int userId}) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('login_token');
      final response = await http.post(
        Uri.parse('$baseUrl/applyOrg/$userId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json'
        },
        body: json.encode({
          'userId': userId,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        print('User applied successfully!');
      }
    } catch (e) {
      throw Exception('Something went wrong while applying ');
    }
  }
}