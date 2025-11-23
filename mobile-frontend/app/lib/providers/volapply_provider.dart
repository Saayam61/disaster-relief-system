import 'package:app/models/volunteer.dart';
import 'package:flutter/material.dart';
import '../services/volapply_api.dart';

class VolapplyProvider with ChangeNotifier {
  List<Volunteer> _volunteers = [];
  Volunteer? _volunteer;
  bool _isLoading = false;
  String? _error;

  List<Volunteer> get volunteers => _volunteers;
  Volunteer? get volunteer => _volunteer;
  bool get isLoading => _isLoading;
  String? get error => _error;

  final VolapplyApi _volunteerApi = VolapplyApi();

  Future<void> fetchCurrentVolunteer() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _volunteer = await _volunteerApi.fetchCurrentVolunteer();
      // print(_volunteer);
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  
  Future<void> updateVolunteer({
    required String skills,
    required String availability,
    required String status
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      await _volunteerApi.updateVolunteer(
        skills: skills,
        availability: availability,
        status: status,
      );

      // Fetch fresh data after update
      await fetchCurrentVolunteer();
    } catch (e) {
      // _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> applyCenter({
    required int userId,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      await _volunteerApi.applyCenter(
        userId: userId
      );
    } catch (e) {
      // _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> applyOrg({
    required int userId,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      await _volunteerApi.applyOrg(
        userId: userId
      );
    } catch (e) {
      // _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}