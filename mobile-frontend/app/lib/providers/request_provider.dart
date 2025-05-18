import 'package:app/models/relief_center.dart';
import 'package:app/models/request.dart';
import 'package:app/services/request_api.dart';
import 'package:flutter/material.dart';

class RequestProvider extends ChangeNotifier {
  List<Request> _requests = [];
  List<Request> get requests => _requests;

  List<ReliefCenter> _centerData = [];
  List<ReliefCenter> get centerData => _centerData;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  RequestApi _requestApi = RequestApi();
  Future<void> getUserRequests() async {
    _isLoading = true;
    notifyListeners();
    try {
      final fetched = await _requestApi.getUserRequests();
      _requests = fetched['requests'];
      _centerData = fetched['parsedCenters'];
    } catch (e, stacktrace) {
      print('Error fetching requests: $e');
      print(stacktrace);
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<void> deleteRequest(String id) async {
    try {
      await _requestApi.deleteRequest(id);
      _requests.removeWhere((c) => c.id == id);
      notifyListeners();
    } catch (e) {
      print('Error deleting contribution: $e');
    }
  }

  // Add a new request
  Future<void> addRequest(Request request) async {
    _isLoading = true;
    notifyListeners();

    try {
      final newRequest = await _requestApi.addRequest(request);
      // Optionally, insert at the start of the list
      _requests.insert(0, newRequest);
    } catch (e, stacktrace) {
      print('Error adding request: $e');
      print(stacktrace);
      throw e;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Update an existing request
  Future<void> updateRequest(Request request) async {
    _isLoading = true;
    notifyListeners();

    try {
      final updatedRequest = await _requestApi.updateRequest(request);
      // Find the index of the existing request and replace it
      final index = _requests.indexWhere((r) => r.id == updatedRequest.id);
      if (index != -1) {
        _requests[index] = updatedRequest;
      }
    } catch (e, stacktrace) {
      print('Error updating request: $e');
      print(stacktrace);
      throw e;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}